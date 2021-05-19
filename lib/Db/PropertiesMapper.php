<?php

namespace OCA\CustomProperties\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

class PropertiesMapper extends QBMapper
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db, LoggerInterface $logger)
    {
        parent::__construct($db, 'properties', Property::class);
        $this->logger = $logger;
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

    /**
     * @return Property[]
     */
    public function findByPathStartsWith(string $propertypath): array {
        $qb = $this->db->getQueryBuilder();

        $IParameter = $qb->createNamedParameter(substr($propertypath, 1) . '%');
        $q = $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->like('propertypath', $IParameter));

        return $this->findEntities($q);
    }
}
