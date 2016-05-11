<?php

/**
 * This file implements the "schema extractor" for MySql.
 */

namespace dbeurive\Backend\Database\SchemaExtractor;
use dbeurive\Backend\Database\Connector\AbstractConnector;

/**
 * Class MySql
 *
 * This class implements the "schema extractor" for MySql.
 *
 * @package dbeurive\Backend\Database\SchemaExtractor
 */

class MySql extends AbstractSchemaExtractor
{
    /**
     * @see AbstractSchemaExtractor
     */
    protected function _getDatabaseSchema(AbstractConnector $inConnector) {
        $result   = array();
        $pdo      = null;
        $outError = null;

        /** @var \PDO $pdo */
        $pdo = $inConnector->getDatabaseHandler(); // Throws an exception if the connexion is not established.

        $dbName = $inConnector->getConfiguration()[\dbeurive\Backend\Database\Connector\MySql::DB_NAME];

        $sql = "select TABLE_NAME from information_schema.tables where TABLE_SCHEMA={$pdo->quote($dbName)}";
        $tables = $pdo->query($sql);
        foreach ($tables as $table) {
            $tableName = $table['TABLE_NAME'];
            $data[$tableName] = array();
            $sql = "desc `$tableName`";
            $_fields = $pdo->query($sql);
            foreach ($_fields as $_field) {
                $result[$tableName][] = $_field['Field'];
            }
        }

        return $result;
    }
}