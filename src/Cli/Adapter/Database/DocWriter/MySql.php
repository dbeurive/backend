<?php

/**
 * This file implements the "documentation writer" for MySql.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\DocWriter;

use dbeurive\Input\Specification;
use dbeurive\Input\SpecificationsSet;
use dbeurive\Backend\Database\Connector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;


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
             ->setDescription("Extract data from a MySql database, and from all API's entry points.");
    }

    /**
     * {@inheritDoc}
     * @see \dbeurive\Backend\Cli\Adapter\Database\DocWriter\AbstractDocWriter
     */
    protected function _getSpecificOptions(InputInterface $input) {
        return [
            Connector\Option::CONNECTOR_NAME => \dbeurive\Backend\Database\Connector\MySql::getFullyQualifiedClassName()
        ];
    }
}