<?php

/**
 * This file implements the base class for all "scheme extractors".
 */

namespace dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor;

use dbeurive\Backend\Database\Doc\ConfigurationParameter as DocOption;
use dbeurive\Backend\Cli\Adapter\Database\Connector;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Backend\Cli\Option as CliOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use dbeurive\Backend\Cli\Adapter\Database\Connector\InterfaceConnector;
use dbeurive\Backend\Cli\Adapter\Database\Connector\AbstractConnector;

/**
 * Class AbstractSchemaExtractor
 *
 * This class is the base class for all "scheme extractors".
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor
 */

abstract class AbstractSchemaExtractor extends Command implements InterfaceSchemaExtractor
{
    /**
     * @var string Name of a sub class of \dbeurive\Backend\Database\SchemaExtractor\AbstractSchemaExtractor
     */
    private $__connectorClassName;
    /**
     * @var array List of configuration's parameters for the database connector.
     */
    private $__connectorParameters;

    /**
     * Return the schema of the database.
     * @param AbstractConnector $inConnector Connector for the database.
     * @return array|false If the operation is successful, then the method returns an array that represents the schema of the database:
     *                     array(   <table name> => array(<field name>, <field name>...),
     *                              <table name> => array(<field name>, <field name>...),
     *                              ...)
     *                     Otherwise, the method throws an exception.
     * @throws \Exception
     */
    abstract protected function _getDatabaseSchema(AbstractConnector $inConnector);

    /**
     * Build the "schema extractor".
     */
    final public function __construct()
    {
        parent::__construct();
        $this->addOption(DocOption::SCHEMA_PATH,              null, InputOption::VALUE_REQUIRED, 'Path to the file that will be used to store the schema')
             ->addOption(CliOption::CONFIG_LOADER_CLASS_NAME, null, InputOption::VALUE_OPTIONAL, 'Fully qualified name of a class used to load the configuration from a source.');

        // $cliHandlerClassName: Name of a sub class of \dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\AbstractSchemaExtractor
        // Example: \dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\MySql
        $cliHandlerClassName         = get_class($this);
        $this->__connectorClassName  = call_user_func("${cliHandlerClassName}::getConnectorClassName");
        $this->__connectorParameters = call_user_func("{$this->__connectorClassName}::getConfigurationParameters");

        // Set the list of configuration's parameters for the connector used by the extractor.
        /** @var array $_parameterSpec */
        foreach ($this->__connectorParameters as $_parameterSpec) {
            $name = $_parameterSpec[InterfaceConnector::OPTION_NAME];
            $description = $_parameterSpec[InterfaceConnector::OPTION_DESCRIPTION];
            $mandatory = $_parameterSpec[InterfaceConnector::OPTION_MANDATORY] ? InputOption::VALUE_REQUIRED : InputOption::VALUE_OPTIONAL;
            $default = $_parameterSpec[InterfaceConnector::OPTION_DEFAULT];

            $this->addOption($name, null, $mandatory, $description, $default);
        }
    }

    /**
     * This method is called by the Symfony's console class.
     * It executes the (specific: MySql, PostgreSql...) CLI adapter.
     *
     * @param InputInterface $input Input interface.
     * @param OutputInterface $output Output interface.
     * @return bool If the execution is successful, then the method returns true.
     *         Otherwise, it returns false.
     *
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        // Load the configuration from a file, if required.
        $configLoaderClass = $input->getOption(CliOption::CONFIG_LOADER_CLASS_NAME);
        $parameters = []; // Not required in PHP... but is sucks otherwise.

        if (! is_null($configLoaderClass)) {

            /** @var \dbeurive\Backend\Cli\InterfaceConfigLoader $loader */
            $loader = new $configLoaderClass();
            $parameters = $loader->load();
        } else {

            // Get the configuration's parameters' values for the connector.
            /** @var array $_parameterSpec */
            $specificParameters = [];
            foreach ($this->__connectorParameters as $_parameterSpec) {
                $name = $_parameterSpec[InterfaceConnector::OPTION_NAME];
                $specificParameters[$name] = $input->getOption($name);
            }

            // The following options contains data used to use the API's entry points.
            $genericParameters = [
                DocOption::SCHEMA_PATH => $input->getOption(DocOption::SCHEMA_PATH)
            ];

            $parameters = array_merge($genericParameters, $specificParameters);
        }

        // Check the configurations.
        $status = call_user_func("{$this->__connectorClassName}::checkConfiguration", $parameters);

        // $status = $this->_checkConfiguration($options);
        if (count($status) > 0) {
            CliWriter::echoError(implode("\n", $status));
            return false;
        }

        // Create a connector.
        /** @var \dbeurive\Backend\Cli\Adapter\Database\Connector\AbstractConnector $connector */
        $connector = new $this->__connectorClassName($parameters);
        $connector->connect();

        // Execute the schema extractor.
        $schema = $this->_getDatabaseSchema($connector);

        // Now, write the schema.
        \dbeurive\Util\UtilData::to_callable_php_file($schema, $parameters[DocOption::SCHEMA_PATH]);
        return true;
    }

}