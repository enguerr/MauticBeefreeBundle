<?php

/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Controller;

use MauticPlugin\MauticBeefreeBundle\Uploader\BeefreeUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;


class AjaxController extends CommonAjaxController
{
    /**
     * @return JsonResponse
     */
    public function uploadAction()
    {
        /** @var BeeFreeUploader $grapesJsUploader */
        $beefreeUploader = $this->get('mautic.beefree.js.uploader');

        return $this->sendJsonResponse(['data'=> $beefreeUploader->uploadFiles($this->request)]);
    }
}
