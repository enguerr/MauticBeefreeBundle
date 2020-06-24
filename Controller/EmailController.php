<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Controller;

use Mautic\CoreBundle\Controller\BuilderControllerTrait;
use Mautic\CoreBundle\Controller\FormController;
use Mautic\CoreBundle\Controller\FormErrorMessagesTrait;
use Mautic\CoreBundle\Helper\EmojiHelper;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\EmailBundle\Controller\EmailController as BaseController;
use Mautic\EmailBundle\Entity\Email;
use Mautic\EmailBundle\Form\Type\ExampleSendType;
use Mautic\LeadBundle\Controller\EntityContactsTrait;
use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeTheme;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeThemeRepository;
use MauticPlugin\MauticCitrixBundle\Helper\CitrixHelper;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends BaseController
{

    /**
     * @param      $objectId
     * @param bool $ignorePost
     * @param bool $forceTypeSelection
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($objectId, $ignorePost = false, $forceTypeSelection = false)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model  = $this->getModel('email');
        $method = $this->request->getMethod();

        $entity  = $model->getEntity($objectId);
        $session = $this->get('session');
        $page    = $this->get('session')->get('mautic.email.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('mautic_email_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'MauticEmailBundle:Email:index',
            'passthroughVars' => [
                'activeLink'    => 'mautic_email_index',
                'mauticContent' => 'email',
            ],
        ];

        //not found
        if ($entity === null) {
            return $this->postActionRedirect(
                array_merge(
                    $postActionVars,
                    [
                        'flashes' => [
                            [
                                'type'    => 'error',
                                'msg'     => 'mautic.email.error.notfound',
                                'msgVars' => ['%id%' => $objectId],
                            ],
                        ],
                    ]
                )
            );
        } elseif (!$this->get('mautic.security')->hasEntityAccess(
            'email:emails:editown',
            'email:emails:editother',
            $entity->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'email');
        }

        //Create the form
        $action = $this->generateUrl('mautic_email_action', ['objectAction' => 'edit', 'objectId' => $objectId]);

        $updateSelect = ($method == 'POST')
            ? $this->request->request->get('emailform[updateSelect]', false, true)
            : $this->request->get(
                'updateSelect',
                false
            );

        if ($updateSelect) {
            // Force type to template
            $entity->setEmailType('template');
        }
        /** @var Form $form */
        $form = $model->createForm($entity, $this->get('form.factory'), $action, ['update_select' => $updateSelect]);

        ///Check for a submitted form and process it
        if (!$ignorePost && $method == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                $formData = $this->request->request->get('emailform');
                if ($valid = $this->isFormValid($form) && $this->isFormValidForWebinar($formData, $form, $entity)) {
                    $content = $entity->getCustomHtml();
                    $entity->setCustomHtml($content);

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash(
                        'mautic.core.notice.updated',
                        [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'mautic_email_index',
                            '%url%'       => $this->generateUrl(
                                'mautic_email_action',
                                [
                                    'objectAction' => 'edit',
                                    'objectId'     => $entity->getId(),
                                ]
                            ),
                        ],
                        'warning'
                    );
                }
            } else {
                //clear any modified content
                $session->remove('mautic.emailbuilder.'.$objectId.'.content');
                //unlock the entity
                $model->unlockEntity($entity);
            }

            $template    = 'MauticEmailBundle:Email:view';
            $passthrough = [
                'activeLink'    => 'mautic_email_index',
                'mauticContent' => 'email',
            ];

            // Check to see if this is a popup
            if (isset($form['updateSelect'])) {
                $template    = false;
                $passthrough = array_merge(
                    $passthrough,
                    [
                        'updateSelect' => $form['updateSelect']->getData(),
                        'id'           => $entity->getId(),
                        'name'         => $entity->getName(),
                        'group'        => $entity->getLanguage(),
                    ]
                );
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                $viewParameters = [
                    'objectAction' => 'view',
                    'objectId'     => $entity->getId(),
                ];

                return $this->postActionRedirect(
                    array_merge(
                        $postActionVars,
                        [
                            'returnUrl'       => $this->generateUrl('mautic_email_action', $viewParameters),
                            'viewParameters'  => $viewParameters,
                            'contentTemplate' => $template,
                            'passthroughVars' => $passthrough,
                        ]
                    )
                );
            } elseif ($valid && $form->get('buttons')->get('apply')->isClicked()) {
                // Rebuild the form in the case apply is clicked so that DEC content is properly populated if all were removed
                $form = $model->createForm($entity, $this->get('form.factory'), $action, ['update_select' => $updateSelect]);
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);

            //clear any modified content
            $session->remove('mautic.emailbuilder.'.$objectId.'.content');

            // Set to view content
            $template = $entity->getTemplate();
            if (empty($template)) {
                $content = $entity->getCustomHtml();
                $form['customHtml']->setData($content);
            }
        }

        $assets         = $form['assetAttachments']->getData();
        $attachmentSize = $this->getModel('asset')->getTotalFilesize($assets);

        $slotTypes   = $model->getBuilderComponents($entity, 'slotTypes');
        $sections    = $model->getBuilderComponents($entity, 'sections');
        $sectionForm = $this->get('form.factory')->create('builder_section');
        $routeParams = [
            'objectAction' => 'edit',
            'objectId'     => $entity->getId(),
        ];
        if ($updateSelect) {
            $routeParams['updateSelect'] = $updateSelect;
            $routeParams['contentOnly']  = 1;
        }

        //set some permissions
        $permissions = $this->get('mautic.security')->isGranted(
            [
                'page:preference_center:viewown',
                'page:preference_center:viewother',
            ],
            'RETURN_ARRAY'
        );
        $bffactory = $this->getEntityManager()->getRepository('MauticBeefreeBundle:BeefreeTheme');
        return $this->delegateView(
            [
                'viewParameters' => [
                    'form'               => $this->setFormTheme($form, 'MauticEmailBundle:Email:form.html.php', 'MauticEmailBundle:FormTheme\Email'),
                    'isVariant'          => $entity->isVariant(true),
                    'slots'              => $this->buildSlotForms($slotTypes),
                    'sections'           => $this->buildSlotForms($sections),
                    'themes'             => $this->factory->getInstalledThemes('email', true),
                    'bfthemes'           => $bffactory->getInstalledThemes('email', true),
                    'email'              => $entity,
                    'forceTypeSelection' => $forceTypeSelection,
                    'attachmentSize'     => $attachmentSize,
                    'builderAssets'      => trim(preg_replace('/\s+/', ' ', $this->getAssetsForBuilder())), // strip new lines
                    'sectionForm'        => $sectionForm->createView(),
                    'permissions'        => $permissions,
                    'previewUrl'         => $this->generateUrl(
                        'mautic_email_preview',
                        ['objectId' => $entity->getId()],
                        true
                    ),
                ],
                'contentTemplate' => 'MauticBeefreeBundle:Email:form.html.php',
                'passthroughVars' => [
                    'activeLink'      => '#mautic_email_index',
                    'mauticContent'   => 'email',
                    'updateSelect'    => InputHelper::clean($this->request->query->get('updateSelect')),
                    'route'           => $this->generateUrl('mautic_email_action', $routeParams),
                    'validationError' => $this->getFormErrorForBuilder($form),
                ],
            ]
        );
    }

}