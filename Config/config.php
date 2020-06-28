<?php

/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

return [
    'name'        => 'Beefree plugin',
    'description' => 'BeeFree integration for Mautic',
    'author'      => 'Enguerr',
    'version'     => '1.0.0',
    'services' => [
        'events'  => [
            'mautic.beefree.js.event.subscriber'=>[
                'class'=> \MauticPlugin\MauticBeefreeBundle\EventListener\EventSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.beefree.repository.beefreeVersion'
                ],
            ],
        ],
        'forms'   => [
            'mautic.beefree.form.type.email' => [
                'class' => \MauticPlugin\MauticBeefreeBundle\Form\Type\EmailType::class,
                'arguments' => 'mautic.factory',
                'alias' => 'emailform'
            ],
            'mautic.form.type.beefree' => array(
                'class'     => 'MauticPlugin\MauticBeefreeBundle\Form\Type\ConfigType',
                'alias'     => 'beefree',
                'arguments' => array(
                    'mautic.helper.core_parameters',
                    'translator',
                ),
            ),
        ],
        'helpers' => [],
        'other'   => [
            'mautic.beefree.js.uploader' => [
                'class'     => MauticPlugin\MauticBeefreeBundle\Uploader\BeefreeUploader::class,
                'arguments' => [
                    'mautic.helper.file_uploader',
                    'mautic.helper.core_parameters',
                    'mautic.helper.paths',
                ],
            ],
        ],
/*        'models'       => [
            'mautic.email.model.beefreeTheme' => [
                'class'     =>   \MauticPlugin\MauticBeefreeBundle\Model\BeefreeThemeModel::class,
                'arguments' => []
            ]
        ],*/
        'integrations' => [
            'mautic.integration.beefree' => [
                'class' => \MauticPlugin\MauticBeefreeBundle\Integration\BeefreeIntegration::class,
            ],
        ],
        'repositories' => [
            'mautic.beefree.repository.beefreeTheme' => [
                'class'     => \MauticPlugin\MauticBeefreeBundle\Entity\BeefreeThemeRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \MauticPlugin\MauticBeefreeBundle\Entity\BeefreeTheme::class,
                ],
            ],
            'mautic.beefree.repository.beefreeVersion' => [
                'class'     => \MauticPlugin\MauticBeefreeBundle\Entity\BeefreeVersionRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \MauticPlugin\MauticBeefreeBundle\Entity\BeefreeVersion::class,
                ],
            ],
        ],
    ],
    'routes'     => [
        'public' => [
            'mautic_beefree_upload' => [
                'path'       => '/beefree/upload',
                'controller' => 'MauticBeefreeBundle:Ajax:upload',
            ],
        ],
        'main' => [
            'mautic_beefree_action' => [
                'path'       => '/beefree/{objectType}/builder/{objectId}',
                'controller' => 'MauticBeefreeBundle:Beefree:builder',
            ],
            'mautic_email_action' => [
                'path'       => '/emails/{objectAction}/{objectId}',
                'controller' => 'MauticBeefreeBundle:BeefreeEmail:execute',
            ],
        ],
    ],
    'menu'       => [],
    'parameters' => [
        'beefree_image_directory'=> 'beefree',
    ],
];
