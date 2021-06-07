<?php

namespace OCA\CustomProperties\Db;

use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class CustomPropertiesMapper extends QBMapper
{
    /**
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, 'customproperties', CustomProperty::class);
    }

    /**
     * @throws \OCP\DB\Exception
     */
    public function findAllForUser($userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->orWhere($qb->expr()->isNull('user_id'));

        return $this->findEntities($qb);
    }

    /**
     * @param int $id
     * @return CustomProperty|Entity
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     * @throws \OCP\DB\Exception
     */
    public function findById(int $id): Entity
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));

        return $this->findEntity($qb);
    }

    /**
     * @return CustomProperty[]|array
     * @throws \OCP\DB\Exception
     */
    public function findAll(): array
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->isNull('user_id'));

        return $this->findEntities($qb);
    }
}
