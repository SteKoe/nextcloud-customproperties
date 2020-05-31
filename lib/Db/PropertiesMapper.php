<?php

namespace OCA\CustomProperties\Db;

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

    public function findAllByPath(string $propertypath, string $uid)
    {
        $qb = $this->db->getQueryBuilder();

        $q = $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->eq('propertypath', $qb->createNamedParameter($propertypath)))
            ->andWhere($qb->expr()->eq('userid', $qb->createNamedParameter($uid)));

        return $this->findEntities($q);
    }

    public function findByPathAndName(string $propertypath, string $propertyname, string $uid): Property
    {
        $qb = $this->db->getQueryBuilder();

        $q = $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->eq('propertypath', $qb->createNamedParameter($propertypath)))
            ->andWhere($qb->expr()->eq('propertyname', $qb->createNamedParameter($propertyname)))
            ->andWhere($qb->expr()->eq('userid', $qb->createNamedParameter($uid)));

        return $this->findEntity($q);
    }
}