<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomAssetsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailEvent;
use Mautic\PageBundle\PageEvents;
use Mautic\PageBundle\Event\PageEvent;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeVersionRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class EventSubscriber extends CommonSubscriber
{
    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;
    private $beefreeVersionRepository;
    protected $request;
    /**
     * AssetSubscriber constructor.
     *
     * @param IntegrationHelper $integrationHelper
     */
    public function __construct(IntegrationHelper $integrationHelper,BeefreeVersionRepository $bv)
    {
        $this->integrationHelper = $integrationHelper;
        $this->beefreeVersionRepository = $bv;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onRequest',10],
            CoreEvents::VIEW_INJECT_CUSTOM_ASSETS => ['injectAssets', -10],
            EmailEvents::EMAIL_POST_SAVE => ['saveEmailVersion',-20],
            PageEvents::PAGE_POST_SAVE => ['savePageVersion',-20],
        ];
    }

    /**
     * @param CustomAssetsEvent $assetsEvent
     */
    public function onRequest(KernelEvent $kernelEvent)
    {
        $this->request = $kernelEvent->getRequest();
    }

    /**
     * @param CustomAssetsEvent $assetsEvent
     */
    public function injectAssets(CustomAssetsEvent $assetsEvent)
    {
        $beefreeIntegration = $this->integrationHelper->getIntegrationObject('Beefree');
        if ($beefreeIntegration && $beefreeIntegration->getIntegrationSettings()->getIsPublished()) {
            $assetsEvent->addScript('plugins/MauticBeefreeBundle/Assets/js/builder.js');
        }
    }
    /**
     * @param CustomAssetsEvent $assetsEvent
     */
    public function saveEmailVersion(EmailEvent $emailEvent)
    {
        $json = $this->request->get('beefree-template');
        $emailForm = $this->request->get('emailform');
        $emailName = $emailForm['name'];
        $content = $emailForm['customHtml'];
        if (!empty($json)) {
            $this->beefreeVersionRepository->saveBeefreeVersion($emailName . ' - ' . date('d/m/Y H:i:s'), $content, $json, $emailEvent->getEmail()->getId(),'email');
        }
    }
    /**
     * @param CustomAssetsEvent $assetsEvent
     */
    public function savePageVersion(PageEvent $pageEvent)
    {
        $json = $this->request->get('beefree-template');
        $pageForm = $this->request->get('page');
        $pageName = $pageForm['title'];
        $content = $pageForm['customHtml'];
        if (!empty($json)) {
            $this->beefreeVersionRepository->saveBeefreeVersion($pageName . ' - ' . date('d/m/Y H:i:s'), $content, $json, $pageEvent->getPage()->getId(),'page');
        }
    }
}
