<?php

namespace OCA\CustomProperties\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1000Date20200522000000 extends SimpleMigrationStep
{
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
    {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('customproperties')) {
            $table = $schema->createTable('customproperties');

            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('propertyname', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('propertylabel', 'string', [
                'notnull' => true,
                'length' => 200
            ]);

            $table->setPrimaryKey(['id']);
        }
        return $schema;
    }
}
