<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ConfigType.
 */
class ConfigType extends AbstractType
{
    /**
     * @var CoreParametersHelper
     */
    protected $parameters;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * ConfigType constructor.
     *
     * @param CoreParametersHelper $parametersHelper
     * @param TranslatorInterface $translator
     */
    public function __construct(CoreParametersHelper $parametersHelper, TranslatorInterface $translator)
    {
        $this->parameters = $parametersHelper;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

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
            'beefree_image_get',
            YesNoButtonGroupType::class,
            [
                'label'      => 'mautic.beefree.config.image.get',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                    'tooltip' => 'mautic.beefree.config.image.get.tooltip',
                ],
                'data' => isset($options['data']['beefree_image_get']) ?
                    (bool) $options['data']['beefree_image_get']
                    : false,
                'empty_data' => false,
                'required' => false,
            ]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ldapconfig';
    }

    // TODO Coming feature: LDAP bind account and Group lookup
//    /**
//     * @return array
//     */
//    private function getAuthenticationChoices()
//    {
//        $choices = $this->authenticationType->getAuthenticationTypes();
//
//        foreach ($choices as $value => $label) {
//            $choices[$value] = $this->translator->trans($label);
//        }
//
//        asort($choices, SORT_NATURAL);
//
//        return $choices;
//    }
}
