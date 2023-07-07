<?php

declare(strict_types=1);

/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Form\Extension;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Mautic\EmailBundle\Form\Type\EmailType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class EmailTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customButtons = [
            [
                'name'  => 'builder',
                'label' => 'mautic.core.builder',
                'attr'  => [
                    'class'   => 'btn btn-default btn-dnd btn-nospin text-primary btn-builder',
                    'icon'    => 'fa fa-cube',
                    'onclick' => "Mautic.launchBuilder('{$this->getBlockPrefix()}', 'email');",
                ],
            ],
            [
                'name'  => 'builder_beefree',
                'label' => 'mautic.beefree.builder',
                'attr'  => [
                    'class'   => 'btn btn-default btn-dnd btn-nospin text-success btn-builder',
                    'icon'    => 'fa fa-beer',
                    'onclick' => "Mautic.launchCustomBuilder('{$this->getBlockPrefix()}', 'email');",
                ],
            ],
        ];

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'pre_extra_buttons' => $customButtons,
            ]
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [EmailType::class];
    }

    public function getBlockPrefix(): string
    {
        return 'emailform';
    }

}
