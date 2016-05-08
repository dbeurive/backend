<?php

/**
 * This file implements the "documentation writer" for MySql.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\DocWriter;

use dbeurive\Input\Specification;
use dbeurive\Input\SpecificationsSet;
use dbeurive\Backend\Database\Link;
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
            ->setDescription("Extract data from a MySql database, and from all API's entry points.")
            ->addOption(Link\MySql::DB_HOST,     null, InputOption::VALUE_OPTIONAL, 'Host that runs the MySql server (default: localhost)', 'localhost')
            ->addOption(Link\MySql::DB_USER,     null, InputOption::VALUE_REQUIRED, 'Database user', null)
            ->addOption(Link\MySql::DB_PASSWORD, null, InputOption::VALUE_OPTIONAL, "User's password", '')
            ->addOption(Link\MySql::DB_PORT,     null, InputOption::VALUE_OPTIONAL, 'Server TCP port (default: 3306)', 3306)
            ->addOption(Link\MySql::DB_NAME,     null, InputOption::VALUE_REQUIRED, 'Name of the MySql database', null);
    }

    /**
     * {@inheritDoc}
     * @see \dbeurive\Backend\Cli\Adapter\Database\DocWriter\AbstractDocWriter
     */
    protected function _getSpecificOptions(InputInterface $input) {
        return [Link\MySql::DB_HOST => $input->getOption(Link\MySql::DB_HOST),
            Link\MySql::DB_USER     => $input->getOption(Link\MySql::DB_USER),
            Link\MySql::DB_PASSWORD => $input->getOption(Link\MySql::DB_PASSWORD),
            Link\MySql::DB_PORT     => $input->getOption(Link\MySql::DB_PORT),
            Link\MySql::DB_NAME     => $input->getOption(Link\MySql::DB_NAME),
            Link\Option::LINK_NAME  => \dbeurive\Backend\Database\Link\MySql::getFullyQualifiedClassName()
        ];
    }

    /**
     * {@inheritDoc}
     * @see \dbeurive\Backend\Cli\Adapter\Database\DocWriter\InterfaceDocWriter
     */
    static public function checkConfiguration(array $inConfiguration) {
        $set = new SpecificationsSet();
        $set->addInputSpecification(new Specification(Link\MySql::DB_HOST,      true, true))
            ->addInputSpecification(new Specification(Link\MySql::DB_USER,      true, true))
            ->addInputSpecification(new Specification(Link\MySql::DB_PASSWORD,  true, true))
            ->addInputSpecification(new Specification(Link\MySql::DB_PORT,      true, true))
            ->addInputSpecification(new Specification(Link\MySql::DB_NAME,      true, true));

        if ($set->check($inConfiguration)) {
            return [];
        }

        return array_values($set->getErrorsOnInputsInIsolationFromTheOthers());
    }
}