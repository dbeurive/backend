<?php

/**
 * This file implements the base class for all entry points (SQL requests and procedures).
 */

namespace dbeurive\Backend\Database\EntryPoints;

/**
 * Class AbstractEntryPoint
 *
 * Base class for all entry points (SQL requests and procedures).
 *
 * @package dbeurive\Backend\Database\EntryPoints
 */
abstract class AbstractEntryPoint
{
    /**
     * @var null|array The schema of the database.
     * @see \dbeurive\Backend\Database\DatabaseInterface
     */
    private $__databaseSchema = null;

    /**
     * @var mixed Handler to the database (ex: an instance of PDO).
     */
    protected $__dbh = null;

    /**
     * Set the handler to the database.
     * @param mixed $inDbh Handler to the database.
     * @throws \Exception
     */
    public function setDbh($inDbh) {
        if (is_null($this->__dbh)) {
            $this->__dbh = $inDbh;
            return;
        }
        throw new \Exception("Improper use of the method " . __METHOD__ . " detected.");
    }

    /**
     * Get the handler to the database.
     * @return mixed The handler to the database.
     */
    protected function getDbh() {
        return $this->__dbh;
    }

    /**
     * Set the schema of the database.
     * @param array $inSchema Schema of the database.
     */
    public function setDatabaseSchema(array $inSchema) {
        $this->__databaseSchema = $inSchema;
    }

    /**
     * Return the list of fields within a given table.
     * @param string $inTableName Name of the table.
     * @return array The list of fields within the table.
     */
    public function getTableFieldsNames($inTableName) {
        return $this->__databaseSchema[$inTableName];
    }

    /**
     * Return the schema of the database.
     * @return array|null
     * @see __databaseSchema
     */
    public function getDatabaseSchema() {
        return $this->__databaseSchema;
    }

    /**
     * AbstractEntryPoint constructor.
     */
    final public function __construct() {}

    /**
     * Return the description of the entry point.
     * @return \dbeurive\Backend\Database\EntryPoints\Description\Sql|\dbeurive\Backend\Database\EntryPoints\Description\Procedure The entry point's description.
     */
    abstract public function getDescription();

    /**
     * Execute the entry point.
     * @param mixed $inConfiguration The configuration for the execution.
     * @return mixed The result of the execution.
     */
    abstract public function execute($inConfiguration);
}