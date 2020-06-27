<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticBeefreeBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomAssetsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailEvent;
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
            EmailEvents::EMAIL_POST_SAVE => ['saveVersion',-20],
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
    public function saveVersion(EmailEvent $emailEvent)
    {
        $json = $this->request->get('beefree-template');
        $emailForm = $this->request->get('emailform');
        $emailName = $emailForm['name'];
        $content = $emailForm['customHtml'];
        $this->beefreeVersionRepository->saveBeefreeVersion($emailName.' - '.date('d/m/Y H:i:s'),$content,$json,$emailEvent->getEmail()->getId());
    }
}
