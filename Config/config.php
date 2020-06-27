<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'Beefree plugin',
    'description' => 'BeeFree integration for Mautic',
    'author'      => 'enguer.com',
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
            ]
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
        'beefree_image_directory'=> 'beefree'
    ],
];
