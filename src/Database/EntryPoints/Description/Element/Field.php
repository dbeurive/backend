<?php

/**
 * This file implements the base class that represents an "element" used to store information about a database's table's field.
 */

namespace dbeurive\Backend\Database\EntryPoints\Description\Element;

/**
 * Class Field
 *
 * This class is the base class that represents an "element" used to store information about a database's table's field.
 *
 * @package dbeurive\Backend\Database\EntryPoints\Description\Element
 */

class Field extends AbstractElement {

    /**
     * @var \dbeurive\Backend\Database\EntryPoints\Description\Element\Table Table that contains the field.
     */
    private $__table = null;
    /**
     * @var string Name of the table that contains the field.
     */
    private $__tableName = null;

    /**
     * Set the field's name.
     * @param string $inName The field's name.
     * @return $this
     * @throws \Exception
     */
    public function setName($inName) {
        $tokens = explode('.', $inName);
        if (count($tokens) != 2) {
            throw new \Exception("Invalid field name \"${inName}\". Fields' names must follow the convention \"table.field\".");
        }
        $this->__tableName = $tokens[0];
        parent::setName($inName);
        $this->__table = Table::getByClassAndName(Table::getFullyQualifiedClassName(), $this->__tableName);
        if (false === $this->__table) {
            throw new \Exception("For field ${inName}: table {$this->__tableName} does not exist !");
        }

        $this->addToRepository($inName);
        return $this;
    }

    /**
     * Return the table that contains this field.
     * @return \dbeurive\Backend\Database\EntryPoints\Description\Element\Table The method returns the table that contains the field.
     */
    public function getTable() {
        return $this->__table;
    }

    /**
     * Return all the fields within a given table identified by its name.
     * @param string|\dbeurive\Backend\Database\EntryPoints\Description\Element\Table $inTable Name of the table, or object that represents the table.
     * @return array The method returns the list of fields within the table identified by the given name.
     */
    public static function getNamesByTable($inTable) {
        $inTable = is_a($inTable, Table::getFullyQualifiedClassName()) ? $inTable->getName() : $inTable;
        $res = [];

        $repository = parent::getRepositoryForClass(__CLASS__);

        foreach ($repository as $_name => $_field) {
            $tokens = explode('.', $_name);
            if ($tokens[0] == $inTable) {
                $res[] = $_name;
            }
        }
        return $res;
    }

    /**
     * {@inheritdoc}
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Element\AbstractElement
     */
    public function __construct($inName, $inId=null) {
        $this->setName($inName);
        if (! is_null($inId)) {
            $this->setId($inId);
        }
    }
}