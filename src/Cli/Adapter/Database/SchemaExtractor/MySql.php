<?php

/**
 * This file implements the "documentation writer" for MySql.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor;

use dbeurive\Input\Specification;
use dbeurive\Input\SpecificationsSet;
use dbeurive\Backend\Database\Connector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;



/**
 * Class MySql
 * @package dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor
 */

class MySql extends AbstractSchemaExtractor {

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:schema-mysql')
            ->setDescription("Extract the schema os a specified MySql database.")
            ->addOption(Connector\MySql::DB_HOST,     null, InputOption::VALUE_OPTIONAL, 'Host that runs the MySql server (default: localhost)', 'localhost')
            ->addOption(Connector\MySql::DB_USER,     null, InputOption::VALUE_REQUIRED, 'Database user', null)
            ->addOption(Connector\MySql::DB_PASSWORD, null, InputOption::VALUE_OPTIONAL, "User's password", '')
            ->addOption(Connector\MySql::DB_PORT,     null, InputOption::VALUE_OPTIONAL, 'Server TCP port (default: 3306)', 3306)
            ->addOption(Connector\MySql::DB_NAME,     null, InputOption::VALUE_REQUIRED, 'Name of the MySql database', null);
    }

    /**
     * {@inheritDoc}
     * @see AbstractSchemaExtractor
     */
    protected function _getSpecificOptions(InputInterface $input) {
        return [Connector\MySql::DB_HOST  => $input->getOption(Connector\MySql::DB_HOST),
            Connector\MySql::DB_USER      => $input->getOption(Connector\MySql::DB_USER),
            Connector\MySql::DB_PASSWORD  => $input->getOption(Connector\MySql::DB_PASSWORD),
            Connector\MySql::DB_PORT      => $input->getOption(Connector\MySql::DB_PORT),
            Connector\MySql::DB_NAME      => $input->getOption(Connector\MySql::DB_NAME)
        ];
    }

    /**
     * {@inheritDoc}
     * @see AbstractSchemaExtractor
     */
    protected function _checkConfiguration(array $inConfiguration) {
        $set = new SpecificationsSet();
        $set->addInputSpecification(new Specification(Connector\MySql::DB_HOST,      true, true))
            ->addInputSpecification(new Specification(Connector\MySql::DB_USER,      true, true))
            ->addInputSpecification(new Specification(Connector\MySql::DB_PASSWORD,  true, true))
            ->addInputSpecification(new Specification(Connector\MySql::DB_PORT,      true, true))
            ->addInputSpecification(new Specification(Connector\MySql::DB_NAME,      true, true));

        if ($set->check($inConfiguration)) {
            return [];
        }

        return array_values($set->getErrorsOnInputsInIsolationFromTheOthers());
    }
}