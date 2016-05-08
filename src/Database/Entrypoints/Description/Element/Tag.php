<?php

/**
 * This file implements the base class that represents an "element" (a piece of information) used to store information about a tag.
 */

namespace dbeurive\Backend\Database\Entrypoints\Description\Element;

/**
 * Class Tag
 *
 * This class represents the base class that represents an "element" (a piece of information) used to store information about a tag.
 *
 * @package dbeurive\Backend\Database\Entrypoints\Description\Element
 */

class Tag extends AbstractElement {

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