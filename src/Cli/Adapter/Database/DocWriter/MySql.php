<?php

/**
 * This file implements the "documentation writer" for MySql.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\DocWriter;

use dbeurive\Backend\Database\Connector;

/**
 * Class MySql
 *
 * This class implements the "documentation writer" targeted for MySql.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\DocWriter
 */

class MySql extends AbstractDocWriter {

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:doc-mysql')
             ->setDescription("Build the documentation from SQL requests written for MySql.");
    }

    /**
     * {@inheritDoc}
     * @see \dbeurive\Backend\Cli\Adapter\Database\DocWriter\AbstractDocWriter
     */
    protected function _getSqlServiceProvider() {
        return '\\dbeurive\\Backend\\Database\\SqlService\\MySql';
    }
}