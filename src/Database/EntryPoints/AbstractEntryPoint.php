<?php

namespace dbeurive\Backend\Database\EntryPoints;

abstract class AbstractEntryPoint
{
    /**
     * @var null|callable Function used to return the list of fields within a given table.
     */
    private $__fieldsProvider = null;

    /**
     * Set the function used to return the list of fields within a given table.
     * @param callable $inFieldsProvider Function used to return the list of fields within a given table.
     * @throws \Exception
     */
    public function setFieldsProvider(callable $inFieldsProvider) {
        if (is_null($this->__fieldsProvider)) {
            $this->__fieldsProvider = $inFieldsProvider;
            return;
        }
        throw new \Exception("Improper use of the method " . __METHOD__ . " detected.");
    }

    /**
     * Return the list of fields within a given table.
     * @param string $inTableName Name of the table.
     * @return array The list of fields within the table.
     */
    public function getTableFieldsNames($inTableName) {
        return call_user_func($this->__fieldsProvider, $inTableName);
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