<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\EmojiHelper;
use Mautic\CoreBundle\Helper\InputHelper;

class BeefreeController extends CommonController
{
    /**
     * Builder.
     *
     * @param string $objectType
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function builderAction($objectType, $objectId)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel|\Mautic\EmailBundle\Model\EmailModel $model */
        $model = $this->getModel($objectType);
        $aclToCheck = 'email:emails:';
        if ($objectType !== 'page') {
            $aclToCheck = 'page:pages:';
        }

        //permission check
        if (strpos($objectId, 'new') !== false) {
            $isNew = true;
            if (!$this->get('mautic.security')->isGranted($aclToCheck.'create')) {
                return $this->accessDenied();
            }

            $entity = $model->getEntity();
            $entity->setSessionId($objectId);
        } else {
            $isNew  = false;
            $entity = $model->getEntity($objectId);
            if ($entity == null
                || !$this->get('mautic.security')->hasEntityAccess(
                    $aclToCheck.'viewown',
                    $aclToCheck.'viewother',
                    $entity->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }
        }

        $template = InputHelper::clean($this->request->query->get('template'));
        $slots    = $this->factory->getTheme($template)->getSlots($objectType);

        //merge any existing changes
        $newContent = $this->get('session')->get('mautic.'.$objectType.'builder.'.$objectId.'.content', []);
        $content    = $entity->getContent();

        if (is_array($newContent)) {
            $content = array_merge($content, $newContent);
            // Update the content for processSlots
            $entity->setContent($content);
        }

        // Replace short codes to emoji
        $content = EmojiHelper::toEmoji($content, 'short');

        $logicalName = $this->factory->getHelper('theme')->checkForTwigTemplate(':'.$template.':'.$objectType.'.html.php');

        $templateWithBody =  $this->renderView(
            $logicalName,
            [
                'isNew'     => $isNew,
                'slots'     => $slots,
                'content'   => $content,
                $objectType => $entity,
                'template'  => $template,
                'basePath'  => $this->request->getBasePath(),
            ]
        );

        /** @var CoreParametersHelper $coreParametersHelpers */
        $coreParametersHelpers = $this->get('mautic.helper.core_parameters');

        $templateDirectory = ($objectType == 'email') ? 'Builder\Email' : 'Builder\Page';

        preg_match("/<body[^>]*>(.*?)<\/body>/is", $templateWithBody, $matches);
        $body = $matches[1];
        $templateWithoutBody = str_replace($body, '||BODY||', $templateWithBody);
        $hiddenTemplate = '<textarea id="templateBuilder" style="display:none">'. $templateWithoutBody.'</textarea>';
        $templateWithoutBody = str_replace('||BODY||', '', $templateWithoutBody);
        $libraries = $this->renderView('MauticBeefreeBundle:'.$templateDirectory.':head.html.php', [
            'siteUrl'     => $coreParametersHelpers->getParameter('site_url'),
        ]);
        $templateForBuilder = str_replace('</head>', $libraries.'</head>', $templateWithoutBody);

        $builderCode = $this->renderView('MauticBeefreeBundle:'.$templateDirectory.':builder.html.php', ['images'=>$this->get('mautic.beefree.js.uploader')->getImages()]);
        $templateForBuilder = str_replace('</body>', $builderCode.$hiddenTemplate.'</body>', $templateForBuilder);


        return $this->render(
            'MauticBeefreeBundle:'.$templateDirectory.':body.html.php',
            [
                'templateForBuilder'     => $templateForBuilder,
            ]
        );

    }
}
