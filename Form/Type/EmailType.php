<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Form\Type;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\CoreBundle\Form\DataTransformer\EmojiToShortTransformer;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Mautic\CoreBundle\Form\EventListener\FormExitSubscriber;
use Mautic\CoreBundle\Form\Type\DynamicContentTrait;
use Mautic\CoreBundle\Form\Type\SortableListType;
use Mautic\EmailBundle\Form\Type\EmailType as BaseEmailType;
use Mautic\LeadBundle\Helper\FormFieldHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EmailType extends BaseEmailType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);

        $customButtons = [
            [
                'name'  => 'builder',
                'label' => 'mautic.core.builder',
                'attr'  => [
                    'class'   => 'btn btn-default btn-dnd btn-nospin text-primary btn-builder',
                    'icon'    => 'fa fa-cube',
                    'onclick' => "Mautic.launchBuilder('emailform', 'email');",
                ],
            ],
            [
                'name'  => 'builder_beefree',
                'label' => 'mautic.beefree.builder',
                'attr'  => [
                    'class'   => 'btn btn-default btn-dnd btn-nospin text-success btn-builder',
                    'icon'    => 'fa fa-beer',
                    'onclick' => "Mautic.launchCustomBuilder('emailform', 'email');",
                ],
            ],
        ];

        $builder->add(
            'buttons',
            'form_buttons',
            [
                'pre_extra_buttons' => $customButtons,
            ]
        );

    }

}