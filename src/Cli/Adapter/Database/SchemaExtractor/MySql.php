<?php

/**
 * This file implements the "schema extractor" for MySql.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor;

use dbeurive\Backend\Database\Connector;

/**
 * Class MySql
 *
 * This class implements the "schema extractor" for MySql.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor
 */

class MySql extends AbstractSchemaExtractor {

    /**
     * {@inheritDoc}
     * @see InterfaceSchemaExtractor
     */
    static public function getExtractorClassName() {
        return '\\dbeurive\\Backend\\Database\\SchemaExtractor\\MySql';
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:schema-mysql')
             ->setDescription("Extract the schema of a specified MySql database.");
    }
}