<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\FormBuilderInterface;


class BeefreeIntegration extends AbstractIntegration
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Beefree';
    }

    public function getIcon(): string
    {
        return 'plugins/MauticBeefreeBundle/Assets/img/icon.png';
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
    /**
     * {@inheritdoc}
     *
     * @param Form|\Symfony\Component\Form\FormBuilder $builder
     * @param array $data
     * @param string $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ($formArea == 'features') {
            $builder->add(
                'beefree_user_name',
                TextType::class,
                [
                    'label'      => 'mautic.beefree.config.username',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                        'tooltip' => 'mautic.beefree.config.api.key.tooltip',
                    ],
                    'empty_data' => ''
                ]
            );
            $builder->add(
                'beefree_api_key',
                TextType::class,
                [
                    'label'      => 'mautic.beefree.config.api.key',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                        'tooltip' => 'mautic.beefree.config.api.key.tooltip',
                    ],
                    'empty_data' => ''
                ]
            );

            $builder->add(
                'beefree_api_secret',
                TextType::class,
                [
                    'label'      => 'mautic.beefree.config.api.secret',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                        'tooltip' => 'mautic.beefree.config.api.secret.tooltip',
                    ],
                    'empty_data' => ''
                ]
            );
            $builder->add(
                'beefree_api_key_page',
                TextType::class,
                [
                    'label'      => 'mautic.beefree.config.page.api.key',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                        'tooltip' => 'mautic.beefree.config.page.api.key.tooltip',
                    ],
                    'empty_data' => ''
                ]
            );
            $builder->add(
                'beefree_api_secret_page',
                TextType::class,
                [
                    'label'      => 'mautic.beefree.config.page.api.secret',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                        'tooltip' => 'mautic.beefree.config.page.api.secret.tooltip',
                    ],
                    'empty_data' => ''
                ]
            );
            $builder->add(
                'beefree_image_get',
                YesNoButtonGroupType::class,
                [
                    'label'      => 'mautic.beefree.config.image.get',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                        'tooltip' => 'mautic.beefree.config.image.get.tooltip',
                    ],
                    'data' => isset($data['beefree_image_get']) ?
                        (bool) $data['beefree_image_get']
                        : false,
                    'empty_data' => false,
                    'required' => false,
                ]
            );


            $builder->add(
                'beefree_image_get',
                YesNoButtonGroupType::class,
                [
                    'label'      => 'mautic.beefree.config.image.get',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                        'tooltip' => 'mautic.beefree.config.image.get.tooltip',
                    ],
                    'data' => isset($data['beefree_image_get']) ?
                        (bool) $data['beefree_image_get']
                        : false,
                    'empty_data' => false,
                    'required' => false,
                ]
            );
        }
    }
}
