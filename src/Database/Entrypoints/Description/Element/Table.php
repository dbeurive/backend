<?php

/**
 * This file implements the base class that represents an "element" (a piece of information) used to store information about database's tables.
 */

namespace dbeurive\Backend\Database\EntryPoints\Description\Element;

/**
 * Class Table
 *
 * This class represents the base class that represents an "element" (a piece of information) used to store information about database's tables.
 *
 * @package dbeurive\Backend\Database\EntryPoints\Description\Element
 */

class Table extends AbstractElement {

    /**
     * {@inheritdoc}
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Element\AbstractElement
     */
    public function __construct($inName, $inId=null) {
        $this->setName($inName);
        $this->addToRepository();
        if (! is_null($inId)) {
            $this->setId($inId);
        }
    }
}