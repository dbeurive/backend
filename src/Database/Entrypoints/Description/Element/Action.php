<?php

/**
 * This file implements an "action" as defined by the concept of "Entity-Relationship Modeling".
 */

namespace dbeurive\Backend\Database\Entrypoints\Description\Element;

/**
 * Class Action
 *
 * The class represents an "action" as defined by the concept of "Entity-Relationship Modeling".
 *
 * @package dbeurive\Backend\Database\Entrypoints\Description\Element
 */

class Action extends AbstractElement {

    /**
     * {@inheritdoc}
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Element\AbstractElement
     */
    public function __construct($inName, $inId=null) {
        $this->setName($inName);
        $this->addToRepository();
        if (! is_null($inId)) {
            $this->setId($inId);
        }
    }
}
