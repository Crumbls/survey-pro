<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Used just to generate table schemas.  We use this in our code generator.
 */
class SchemaService
{
    protected array $schema = [];

    public function getTableSchema(string $modelName): array
    {
        if (!empty($this->schema[$modelName])) {
            return $this->schema[$modelName];
        }

        $model = new $modelName;
        $table = $model->getTable();

        $columns = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->introspectTable($table)
            ->getColumns();

        foreach ($columns as $column) {
            $this->schema[$modelName][$column->getName()] = [
                'type' => $column->getType()->getName(),
                'length' => $column->getLength(),
                'nullable' => !$column->getNotnull(),
                'default' => $column->getDefault(),
                'unsigned' => $column->getUnsigned() ?? false,
                'autoIncrement' => $column->getAutoincrement(),
                'comment' => $column->getComment(),
            ];
        }

        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);

        $this->schema[$modelName]['_indexes'] = [];
        foreach ($indexes as $index) {
            $this->schema[$modelName]['_indexes'][$index->getName()] = [
                'columns' => $index->getColumns(),
                'type' => $index->isPrimary() ? 'primary' :
                    ($index->isUnique() ? 'unique' : 'index'),
            ];
        }

        $foreignKeys = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys($table);

        $this->schema[$modelName]['_foreign_keys'] = [];
        foreach ($foreignKeys as $foreignKey) {
            $this->schema[$modelName]['_foreign_keys'][$foreignKey->getName()] = [
                'local_columns' => $foreignKey->getLocalColumns(),
                'foreign_table' => $foreignKey->getForeignTableName(),
                'foreign_columns' => $foreignKey->getForeignColumns(),
                'on_delete' => $foreignKey->getOptions()['onDelete'] ?? null,
                'on_update' => $foreignKey->getOptions()['onUpdate'] ?? null,
            ];
        }

        return $this->schema[$modelName];
    }

    public function getColumnSchema(string $modelName, string $column): ?array
    {
        return $this->getTableSchema($modelName)[$column] ?? null;
    }
}
