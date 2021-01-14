<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\EmailBundle\Entity\Email;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticBeefreeBundle\Entity\BeefreeThemeRepository;

/**
 * @ORM\Entity(repositoryClass="MauticPlugin\MauticBeefreeBundle\Entity\BeefreeVersionRepository")
 */
class BeefreeVersion
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
     * @var int
     */
    private $object_id;
    /**
     * @var string
     */
    private $preview;

    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $content = '{}';

    /**
     * @var string
     */
    private $json;

    public function __construct()
    {
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('beefree_version')
            ->setCustomRepositoryClass(BeefreeVersionRepository::class);

        $builder->addId();

        $builder->createField('name', 'string')
            ->columnName('name')
            ->build();

        $builder->createField('type', 'string')
            ->columnName('type')
            ->build();

        $builder->createField('preview', 'longblob')
            ->columnName('preview')
            ->build();

        $builder->createField('content', 'longblob')
            ->columnName('content')
            ->build();

        $builder->createField('json', 'text')
            ->columnName('json')
            ->build();

        //fake ManyToOne
        $builder->createField('object_id', 'integer')
            ->columnName('object_id')
            ->build();
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPreview()
    {
        return stream_get_contents($this->preview);
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
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }
    /**
     * @param mixed $json
     */
    public function setJson($json)
    {
        $this->json = $json;
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
