<?php

/**
 * This file implements the "schema extractor" for MySql.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor;

use dbeurive\Backend\Cli\Adapter\Database\Connector\AbstractConnector;

/**
 * Class MySql
 *
 * This class implements the "schema extractor" for MySql.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor
 */

class MySql extends AbstractSchemaExtractor {

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:schema-mysql')
             ->setDescription("Extract the schema of a specified MySql database.");
    }

    /**
     * {@inheritDoc}
     * @see \dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\InterfaceSchemaExtractor
     */
    static public function getConnectorClassName() {
        return '\\dbeurive\\Backend\\Cli\\Adapter\\Database\\Connector\\MySqlPdo';
    }

    /**
     * {@inheritDoc}
     * @see \dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\AbstractSchemaExtractor
     */
    protected function _getDatabaseSchema(AbstractConnector $inConnector) {
        $result   = array();
        $pdo      = null;
        $outError = null;

        /** @var \PDO $pdo */
        $pdo = $inConnector->getDatabaseHandler(); // Throws an exception if the connexion is not established.

        $dbName = $inConnector->getConfiguration()[\dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo::DB_NAME];

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