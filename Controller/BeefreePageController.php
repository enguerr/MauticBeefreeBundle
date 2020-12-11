<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Controller;

use Mautic\CoreBundle\Controller\BuilderControllerTrait;
use Mautic\CoreBundle\Controller\FormController;
use Mautic\CoreBundle\Controller\FormErrorMessagesTrait;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\PageBundle\Controller\PageController as BaseController;
use Mautic\PageBundle\Entity\Page;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeTheme;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeVersion;
use MauticPlugin\MauticCitrixBundle\Helper\CitrixHelper;
use Symfony\Component\HttpFoundation\JsonResponse;

class BeefreePageController extends BaseController
{

    /**
     * Generates new form and processes post data.
     *
     * @param \Mautic\PageBundle\Entity\Page|null $entity
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction($entity = null,$version=null)
    {
        /** @var \Mautic\PageBundle\Model\PageModel $model */
        $model = $this->getModel('page.page');

        if (!($entity instanceof Page)) {
            /** @var \Mautic\PageBundle\Entity\Page $entity */
            $entity = $model->getEntity();
        }

        $method  = $this->request->getMethod();
        $session = $this->get('session');
        if (!$this->get('mautic.security')->isGranted('page:pages:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page   = $session->get('mautic.page.page', 1);
        $action = $this->generateUrl('mautic_page_action', ['objectAction' => 'new']);

        //create the form
        $form = $model->createForm($entity, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if ($method == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $content = $entity->getCustomHtml();
                    $entity->setCustomHtml($content);

                    //form is valid so process the data
                    $model->saveEntity($entity);

                    $this->addFlash('mautic.core.notice.created', [
                        '%name%'      => $entity->getTitle(),
                        '%menu_link%' => 'mautic_page_index',
                        '%url%'       => $this->generateUrl('mautic_page_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]),
                    ]);

                    if ($form->get('buttons')->get('save')->isClicked()) {
                        $viewParameters = [
                            'objectAction' => 'view',
                            'objectId'     => $entity->getId(),
                        ];
                        $returnUrl = $this->generateUrl('mautic_page_action', $viewParameters);
                        $template  = 'MauticPageBundle:Page:view';
                    } else {
                        //return edit view so that all the session stuff is loaded
                        return $this->editAction($entity->getId(), true);
                    }
                }
            } else {
                $viewParameters = ['page' => $page];
                $returnUrl      = $this->generateUrl('mautic_page_index', $viewParameters);
                $template       = 'MauticPageBundle:Page:index';
                //clear any modified content
                $session->remove('mautic.pagebuilder.'.$entity->getSessionId().'.content');
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect([
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => $viewParameters,
                    'contentTemplate' => $template,
                    'passthroughVars' => [
                        'activeLink'    => 'mautic_page_index',
                        'mauticContent' => 'page',
                    ],
                ]);
            }
        }

        $slotTypes   = $model->getBuilderComponents($entity, 'slotTypes');
        $sections    = $model->getBuilderComponents($entity, 'sections');
        $sectionForm = $this->get('form.factory')->create('builder_section');

        $bfrepo = $this->getDoctrine()->getRepository(BeefreeTheme::class);
        $bvrepo = $this->getDoctrine()->getRepository(BeefreeVersion::class);
        /*if ($version){
            $lastversion = $version;
        }else{*/
        $lastversion = $bvrepo->getLastVersion($entity->getId());
        /*}*/
        $postversion = $this->request->request->get('beefree-template');

        //set some permissions
        $permissions = $this->get('mautic.security')->isGranted(
            [
                'page:preference_center:editown',
                'page:preference_center:editother',
            ],
            'RETURN_ARRAY'
        );
        if ($version){
            $lastversion = $version;
        }else{
            $lastversion = $bvrepo->getLastVersion($entity->getId());
        }
        return $this->delegateView([
            'viewParameters' => [
                'form'          => $this->setFormTheme($form, 'MauticBeefreeBundle:Page:form.html.php', 'MauticBeefreeBundle:FormTheme\Page'),
                'isVariant'     => $entity->isVariant(true),
                'tokens'        => $model->getBuilderComponents($entity, 'tokens'),
                'activePage'    => $entity,
                'themes'        => $this->factory->getInstalledThemes('page', true),
                'slots'         => $this->buildSlotForms($slotTypes),
                'sections'      => $this->buildSlotForms($sections),
                'bfthemes'      => $bfrepo->getInstalledThemes('email', true),
                'version_post'  => $postversion,
                'version'       => $lastversion,
                'builderAssets' => trim(preg_replace('/\s+/', ' ', $this->getAssetsForBuilder())), // strip new lines
                'sectionForm'   => $sectionForm->createView(),
                'permissions'   => $permissions,
            ],
            'contentTemplate' => 'MauticBeefreeBundle:Page:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_page_index',
                'mauticContent' => 'page',
                'route'         => $this->generateUrl('mautic_page_action', [
                    'objectAction' => 'new',
                ]),
                'validationError' => $this->getFormErrorForBuilder($form),
            ],
        ]);
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($objectId, $ignorePost = false)
    {
        /** @var \Mautic\PageBundle\Model\PageModel $model */
        $model    = $this->getModel('page.page');
        $security = $this->get('mautic.security');
        $entity   = $model->getEntity($objectId);
        $session  = $this->get('session');
        $page     = $this->get('session')->get('mautic.page.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('mautic_page_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'MauticPageBundle:Page:index',
            'passthroughVars' => [
                'activeLink'    => 'mautic_page_index',
                'mauticContent' => 'page',
            ],
        ];

        //not found
        if ($entity === null) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'mautic.page.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ])
            );
        } elseif (!$security->hasEntityAccess(
                'page:pages:viewown', 'page:pages:viewother', $entity->getCreatedBy()
            ) ||
            ($entity->getIsPreferenceCenter() && !$security->hasEntityAccess(
                    'page:preference_center:viewown', 'page:preference_center:viewother', $entity->getCreatedBy()
                ))) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'page.page');
        }

        //Create the form
        $action = $this->generateUrl('mautic_page_action', ['objectAction' => 'edit', 'objectId' => $objectId]);
        $form   = $model->createForm($entity, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if (!$ignorePost && $this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $content = $entity->getCustomHtml();
                    $entity->setCustomHtml($content);

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash('mautic.core.notice.updated', [
                        '%name%'      => $entity->getTitle(),
                        '%menu_link%' => 'mautic_page_index',
                        '%url%'       => $this->generateUrl('mautic_page_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]),
                    ]);
                }
            } else {
                //clear any modified content
                $session->remove('mautic.pagebuilder.'.$objectId.'.content');
                //unlock the entity
                $model->unlockEntity($entity);
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                $viewParameters = [
                    'objectAction' => 'view',
                    'objectId'     => $entity->getId(),
                ];

                return $this->postActionRedirect(
                    array_merge($postActionVars, [
                        'returnUrl'       => $this->generateUrl('mautic_page_action', $viewParameters),
                        'viewParameters'  => $viewParameters,
                        'contentTemplate' => 'MauticPageBundle:Page:view',
                    ])
                );
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);

            //clear any modified content
            $session->remove('mautic.pagebuilder.'.$objectId.'.content');

            //set the lookup values
            $parent = $entity->getTranslationParent();
            if ($parent && isset($form['translationParent_lookup'])) {
                $form->get('translationParent_lookup')->setData($parent->getTitle());
            }

            // Set to view content
            $template = $entity->getTemplate();
            if (empty($template)) {
                $content = $entity->getCustomHtml();
                $form['customHtml']->setData($content);
            }
        }

        $slotTypes   = $model->getBuilderComponents($entity, 'slotTypes');
        $sections    = $model->getBuilderComponents($entity, 'sections');
        $sectionForm = $this->get('form.factory')->create('builder_section');

        $bfrepo = $this->getDoctrine()->getRepository(BeefreeTheme::class);
        $bvrepo = $this->getDoctrine()->getRepository(BeefreeVersion::class);
        /*if ($version){
            $lastversion = $version;
        }else{*/
            $lastversion = $bvrepo->getLastVersion($entity->getId(),'page');
        /*}*/
        $postversion = $this->request->request->get('beefree-template');


        return $this->delegateView([
            'viewParameters' => [
                'form'          => $this->setFormTheme($form, 'MauticBeefreeBundle:Page:form.html.php', 'MauticBeefreeBundle:FormTheme\Page'),
                'isVariant'     => $entity->isVariant(true),
                'tokens'        => $model->getBuilderComponents($entity, 'tokens'),
                'activePage'    => $entity,
                'themes'        => $this->factory->getInstalledThemes('page', true),
                'slots'         => $this->buildSlotForms($slotTypes),
                'sections'      => $this->buildSlotForms($sections),
                'bfthemes'      => $bfrepo->getInstalledThemes('email', true),
                'version_post'  => $postversion,
                'version'       => $lastversion,
                'builderAssets' => trim(preg_replace('/\s+/', ' ', $this->getAssetsForBuilder())), // strip new lines
                'sectionForm'   => $sectionForm->createView(),
                'previewUrl'    => $this->generateUrl('mautic_page_preview', ['id' => $objectId], true),
                'permissions'   => $security->isGranted(
                    [
                        'page:preference_center:editown',
                        'page:preference_center:editother',
                    ],
                    'RETURN_ARRAY'
                ),
                'security'      => $security,
            ],
            'contentTemplate' => 'MauticBeefreeBundle:Page:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_page_index',
                'mauticContent' => 'page',
                'route'         => $this->generateUrl('mautic_page_action', [
                    'objectAction' => 'edit',
                    'objectId'     => $entity->getId(),
                ]),
                'validationError' => $this->getFormErrorForBuilder($form),
            ],
        ]);
    }

    /**
     * Clone an entity.
     *
     * @param int $objectId
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function cloneAction($objectId)
    {
        /** @var \Mautic\PageBundle\Model\PageModel $model */
        $model  = $this->getModel('page.page');
        $entity = $model->getEntity($objectId);

        if ($entity != null) {
            if (!$this->get('mautic.security')->isGranted('page:pages:create') ||
                !$this->get('mautic.security')->hasEntityAccess(
                    'page:pages:viewown', 'page:pages:viewother', $entity->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }

            $entity = clone $entity;
            $entity->setHits(0);
            $entity->setUniqueHits(0);
            $entity->setRevision(0);
            $entity->setVariantStartDate(null);
            $entity->setVariantHits(0);
            $entity->setIsPublished(false);

            $session     = $this->get('session');
            $contentName = 'mautic.pagebuilder.'.$entity->getSessionId().'.content';

            $session->set($contentName, $entity->getCustomHtml());

            //getversion if exists
            $bvrepo = $this->getDoctrine()->getRepository(BeefreeVersion::class);
            $lastversion = $bvrepo->getLastVersion($objectId,'page');
        }

        return $this->newAction($entity,$lastversion);
    }
}