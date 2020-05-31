<?php

namespace OCA\CustomProperties\Db;

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

    public function findAllForUser($userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->orWhere($qb->expr()->isNull('user_id'));

        return $this->findEntities($qb);
    }

    public function findById(int $id)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));

        return $this->findEntity($qb);
    }

    public function findAll()
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->isNull('user_id'));

        return $this->findEntities($qb);
    }
}