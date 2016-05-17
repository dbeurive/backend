<?php

/**
 * This file implements the base class for all "schema extractor".
 *
 * Schema extractors are software components that return all the fields of a given database.
 */

namespace dbeurive\Backend\Database\SchemaExtractor;
use dbeurive\Backend\Cli\Adapter\Database\Connector\AbstractConnector;

/**
 * Class AbstractSchemaExtractor
 *
 * The class is the base class for all "schema extractor".
 *
 * @package dbeurive\Backend\Database\SchemaExtractor
 */
abstract class AbstractSchemaExtractor implements InterfaceExtractor
{
    /** @var AbstractConnector Connector for the database. */
    private $__connector;

    /**
     * AbstractSchemaExtractor constructor.
     * @param AbstractConnector $inConnector Connector for the database.
     */
    final public function __construct(AbstractConnector $inConnector)
    {
        $this->__connector = $inConnector;
    }

    /**
     * Return the schema of the database.
     * @param AbstractConnector $inConnector Connector for the database.
     * @return array|false If the operation is successful, then the method returns an array that represents the schema of the database:
     *                     array(   <table name> => array(<field name>, <field name>...),
     *                              <table name> => array(<field name>, <field name>...),
     *                              ...)
     *                     Otherwise, the method throws an exception.
     * @throws \Exception
     */
    abstract protected function _getDatabaseSchema(AbstractConnector $inConnector);

    /**
     * Return the schema of the database.
     * @return array|false If the operation is successful, then the method returns an array that represents the schema of the database:
     *                     array(   <table name> => array(<field name>, <field name>...),
     *                              <table name> => array(<field name>, <field name>...),
     *                              ...)
     *                     Otherwise, the method throws an exception.
     * @throws \Exception
     */
    public function getDatabaseSchema() {
        return $this->_getDatabaseSchema($this->__connector);
    }
}