<?php

/**
 * This file implements the "schema extractor" for MySql.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor;

use dbeurive\Backend\Database\Connector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use dbeurive\Backend\Database\Connector\InterfaceConnector;

/**
 * Class MySql
 *
 * This class implements the "schema extractor" for MySql.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor
 */

class MySql extends AbstractSchemaExtractor {

    static public function getConnectorClassName() {
        return '\\dbeurive\\Backend\\Database\\Connector\\MySqlPdo';
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:schema-mysql')
             ->setDescription("Extract the schema of a specified MySql database.");

        /** @var array $_parameterSpec */
        foreach (Connector\MySqlPdo::getConfigurationParameters() as $_parameterSpec) {
            $name = $_parameterSpec[InterfaceConnector::OPTION_NAME];
            $description = $_parameterSpec[InterfaceConnector::OPTION_DESCRIPTION];
            $mandatory = $_parameterSpec[InterfaceConnector::OPTION_MANDATORY] ? InputOption::VALUE_REQUIRED : InputOption::VALUE_OPTIONAL;
            $default = $_parameterSpec[InterfaceConnector::OPTION_DEFAULT];

            $this->addOption($name, null, $mandatory, $description, $default);
        }
    }

    /**
     * {@inheritDoc}
     * @see AbstractSchemaExtractor
     */
    protected function _getSpecificCliParametersValues(InputInterface $input) {
        $params = [];

        /** @var array $_parameterSpec */
        foreach (Connector\MySqlPdo::getConfigurationParameters() as $_parameterSpec) {
            $name = $_parameterSpec[InterfaceConnector::OPTION_NAME];
            $params[$name] = $input->getOption($name);
        }
        return $params;
    }
}