<?php

/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\EmailBundle\Entity\Email;
use Mautic\LeadBundle\Entity\Lead;

/**
 * Class BeefreeTheme.
 */
class BeefreeTheme
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ArrayCollection
     */
    private $email;
    /**
     * @var string
     */
    private $preview;

    /**
     * @var string
     */
    private $content;

    public function __construct()
    {
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('beefree_theme')
            ->setCustomRepositoryClass('MauticBeefreeBundle\Entity\BeefreeThemeRepository');

        $builder->addId();

        $builder->createField('name', 'string')
            ->columnName('name')
            ->build();

        $builder->createField('preview', 'blob')
            ->columnName('preview')
            ->build();

        $builder->createField('content', 'text')
            ->columnName('content')
            ->build();

        #TODO
        /*$builder->createOneToMany('email', 'Email')
            ->setIndexBy('id')
            ->mappedBy('email')
            ->cascadePersist()
            ->fetchExtraLazy()
            ->build();*/

    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('stat')
            ->addProperties(
                [
                    'id',
                    'name',
                    'preview',
                    'content',
                ]
            )
            ->build();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param mixed $preview
     */
    public function setPreview($preview)
    {
        $this->preview= $preview;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setEmail($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
