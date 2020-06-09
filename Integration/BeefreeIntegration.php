<?php


/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticGrapeJsBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class BeefreeIntegration extends AbstractIntegration
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'GrapeJs';
    }

    public function getIcon()
    {
        return 'plugins/MauticGrapeJsBundle/Assets/img/icon.png';
    }

    /**
     * @return array
     */
    public function getFormSettings()
    {
        return [
            'requires_callback'      => false,
            'requires_authorization' => false,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }
}
