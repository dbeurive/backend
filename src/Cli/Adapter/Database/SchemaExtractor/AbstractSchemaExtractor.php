<?php

/**
 * This file implements the base class for all "scheme extractors".
 */

namespace dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor;

use dbeurive\Backend\Database\Doc\Option as DocOption;
use dbeurive\Backend\Database\Connector;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Backend\Cli\Option as CliOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractSchemaExtractor
 *
 * This class is the base class for all "scheme extractors".
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor
 */

abstract class AbstractSchemaExtractor extends Command
{
    /**
     * Build the "schema extractor".
     */
    public function __construct()
    {
        parent::__construct();
        $this->addOption(DocOption::SCHEMA_PATH, null, InputOption::VALUE_REQUIRED, 'Path to the file that will be used to store the schema')
             ->addOption(CliOption::CONFIG_LOADER_CLASS_NAME,    null, InputOption::VALUE_OPTIONAL, 'Fully qualified name of a class used to load the configuration from a source.');

        // Options for the specific database' adapter will be added by the (child) class that handles the specific database' adapter.
    }

    /**
     * Extract the specific configuration from the command line for the "schema extractors".
     * Please note that all "schema extractors" share some common configuration's parameters:
     *    * \dbeurive\Backend\Database\Doc\Option::SCHEMA_PATH
     *    * \dbeurive\Backend\Cli\Option::CONFIG_LOADER_CLASS_NAME
     *
     * @param InputInterface $input Input interface as defined be the Symfony console interface.
     * @return array The method returns an associative array.
     *         The array's keys are the names of the configuration parameters.
     *         The array's values are the configuration parameters' values.
     *
     * @see \dbeurive\Backend\Database\Doc\Option::SCHEMA_PATH
     * @see \dbeurive\Backend\Cli\Option::CONFIG_LOADER_CLASS_NAME
     */
    abstract protected function _getSpecificOptions(InputInterface $input);

    /**
     * Check the configuration for the specific "schema extractor" being executed.
     * @param array $inConfiguration List of parameters that define the configuration to check.
     * @return array If the given configuration is valid, then the method returns an empty array.
     *         Otherwise, the method returns a list of error messages.
     */
    abstract protected function _checkConfiguration(array $inConfiguration);

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
        $options = []; // Not required in PHP... but is sucks otherwise.

        if (! is_null($configLoaderClass)) {

            /** @var \dbeurive\Backend\Cli\InterfaceConfigLoader $loader */
            $loader = new $configLoaderClass();
            $options = $loader->load();
        } else {

            // Options from te child class (that handles the specific CLI adapter).
            // These options define values used to connect to the database server.
            $specificOptions = $this->_getSpecificOptions($input);

            // The following options contains data used to use the API's entry points.
            $genericOptions = [
                DocOption::SCHEMA_PATH => $input->getOption(DocOption::SCHEMA_PATH)
            ];

            $options = array_merge($genericOptions, $specificOptions);
        }

        // Check the configurations.
        $status = $this->_checkConfiguration($options);
        if (count($status) > 0) {
            CliWriter::echoError(implode("\n", $status));
            return false;
        }

        // Create a connector.
        $connector = new \dbeurive\Backend\Database\Connector\MySqlPdo($options);
        $connector->connect();

        // Create the schema extractor.
        $extractor = new \dbeurive\Backend\Database\SchemaExtractor\MySql($connector);

        // Execute the schema extractor.
        $schema = $extractor->getDatabaseSchema();

        // Now, write the schema.
        \dbeurive\Util\UtilData::to_callable_php_file($schema, $options[DocOption::SCHEMA_PATH]);
        return true;
    }

}