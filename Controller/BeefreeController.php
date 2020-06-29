<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\EmojiHelper;
use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeTheme;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeVersion;


class BeefreeController extends CommonController
{
    private $parametersHelper;

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
        $integrationHelper = $this->get('mautic.helper.integration');
        $integrations      = $integrationHelper->getIntegrationObjects(null, [], true, null, true);
        $beefree = $integrations['Beefree'];
        $settings = $beefree->getIntegrationSettings();
        $featureSettings = $settings->getFeatureSettings();

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

        //get template content
        $bfrepo = $this->getDoctrine()->getRepository(BeefreeTheme::class);
        $bvrepo = $this->getDoctrine()->getRepository(BeefreeVersion::class);
        $activetemplate = $bfrepo->getNewTemplate();
        switch ($template){
            case "new":
                $contenttemplate = $bvrepo->getNewVersion();
                $contenttemplate->setJson($activetemplate->getContent());
                break;
            case "undefined":
            case "current":
                $contenttemplate = null;
                break;
            default:
                $contenttemplate = $bvrepo->getNewVersion();
                $contenttemplate->setJson($bfrepo->getTheme($template)->getContent());
                break;
        }

        $builderCode = $this->renderView('MauticBeefreeBundle:'.$templateDirectory.':builder.html.php', [
            'images'=>$this->get('mautic.beefree.js.uploader')->getImages(),
            'apikey' => $featureSettings['beefree_api_key'],
            'apisecret' => $featureSettings['beefree_api_secret'],
            'template' => $template,
            'contenttemplate'  => ($contenttemplate)?$contenttemplate->getJson():'JSON.parse(base64decode(mQuery(\'textarea.template-builder-html\', window.parent.document).val()))',
        ]);
        $templateForBuilder = str_replace('</body>', $builderCode.$hiddenTemplate.'</body>', $templateForBuilder);


        return $this->render(
            'MauticBeefreeBundle:'.$templateDirectory.':body.html.php',
            [
                'templateForBuilder'     => $templateForBuilder,
            ]
        );

    }
}
