<?php

namespace OCA\CustomProperties\Db;

use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\IMapperException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class PropertiesMapper extends QBMapper
{
    /**
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, 'properties', Property::class);
    }

    /**
     * @param string $propertypath
     * @param string $propertyname
     * @param string $uid
     * @return Entity|null
     */
    public function findByPathAndName(string $propertypath, string $propertyname, string $uid): ?Entity
    {
        $qb = $this->db->getQueryBuilder();

        $q = $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->eq('propertypath', $qb->createNamedParameter($propertypath)))
            ->andWhere($qb->expr()->eq('propertyname', $qb->createNamedParameter($propertyname)))
            ->andWhere($qb->expr()->eq('userid', $qb->createNamedParameter($uid)));

        try {
            return $this->findEntity($q);
        } catch (IMapperException $e) {
            return null;
        }
    }

    /**
     * @param string $propertyvalue
     * @param string $uid
     * @return Property[]
     */
    public function findByPropertyValueLike(string $propertyvalue, string $uid, int $offset = 0, int $limit = 50): array
    {
        $qb = $this->db->getQueryBuilder();

        $q = $qb->select('*')
            ->from($this->tableName)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('propertypath', 'DESC')
            ->addOrderBy('propertyname', 'DESC')
            ->where($qb->expr()->iLike('propertyvalue', $qb->createNamedParameter("%" . $propertyvalue . "%")))
            ->andWhere($qb->expr()->eq('userid', $qb->createNamedParameter($uid)));

        return $this->findEntities($q);
    }

    /**
     * @param string $propertyvalue
     * @param string $uid
     * @return Property[]
     */
    public function findByPropertyNameLikeAndPropertyValueLike(string $propertyname, string $propertyvalue, string $uid, int $offset = 0, int $limit = 50): array
    {
        $qb = $this->db->getQueryBuilder();

        $q = $qb->select('*')
            ->from($this->tableName)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('propertypath', 'DESC')
            ->addOrderBy('propertyvalue', 'DESC')
            ->where($qb->expr()->iLike('propertyname', $qb->createNamedParameter("%" . $propertyname . "%")))
            ->andwhere($qb->expr()->iLike('propertyvalue', $qb->createNamedParameter("%" . $propertyvalue . "%")))
            ->andWhere($qb->expr()->eq('userid', $qb->createNamedParameter($uid)));

        return $this->findEntities($q);
    }

}
