<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */

namespace MauticPlugin\MauticBeefreeBundle\Entity;

use Doctrine\ORM\NoResultException;
use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * Class BeefreeVersionRepository.
 */

class BeefreeVersionRepository extends CommonRepository
{
    /**
     * @param $hash
     * @param $subject
     * @param $body
     */
    public function saveBeefreeVersion($name, $content,$json,$object_id,$type='email')
    {
        $version = new BeefreeVersion();
        $version->setName($name);
        $version->setEmail($content);
        $version->setJson($json);
        $version->setType($type);
        $version->setObjectId($object_id);

        $this
            ->getEntityManager()
            ->persist($version);

        $this
            ->getEntityManager()
            ->flush();
    }

    /**
     * @param string $string  object_id
     *
     * @return BeefreeVersion
     */
    public function getLastVersion($object_id,$type='email')
    {

        $q = $this->createQueryBuilder($this->getTableAlias());
        $q->andWhere(
            $q->expr()->eq($this->getTableAlias().'.object_id', ':object_id')
        )
            ->andWhere(
                $q->expr()->eq($this->getTableAlias().'.type', ':type')
            )
            ->setParameter('object_id', $object_id)
            ->setParameter('type', $type)
            ->orderBy($this->getTableAlias().'.id','DESC')
            ->setFirstResult(0)
            ->setMaxResults(1);

        try {
            $result = $q->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $result = null;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'bv';
    }
    /**
     * @param string $string  name
     *
     * @return array
     */
    public function getTheme($string)
    {

        $q = $this->createQueryBuilder($this->getTableAlias());
        $q->where(
            $q->expr()->eq($this->getTableAlias().'.name', ':name')
        )
            ->setParameter('name', $string);

        try {
            $result = $q->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $result = null;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getNewVersion()
    {
        return new BeefreeVersion();
    }

}
