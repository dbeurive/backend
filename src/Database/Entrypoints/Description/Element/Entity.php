<?php

/**
 * This file implements an "entity" as defined by the concept of "Entity-Relationship Modeling".
 */


namespace dbeurive\Backend\Database\EntryPoints\Description\Element;

/**
 * Class Entity
 *
 * This class represents an "entity" as defined by the concept of "Entity-Relationship Modeling".
 *
 * @package dbeurive\Backend\Database\EntryPoints\Description\Element
 */

class Entity extends AbstractElement {

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
