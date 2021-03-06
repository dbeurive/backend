<?php

/**
 * This file implements the CLI interface to the documentation writer.
 * "Documentation writers" perform the following actions:
 *     1. Extract information from all the API's entry points.
 *     2. Organize the information previously extracted.
 */

namespace dbeurive\Backend\Cli\Bin;

use dbeurive\Backend\Database\EntryPoints\ConfigurationParameter as EntryPointOption;
use dbeurive\Backend\Database\Doc\ConfigurationParameter as DocOption;
use dbeurive\Backend\Database\Doc\Writer;
use dbeurive\Backend\Cli\Adapter\Database\Connector;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Backend\Cli\Option as CliOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class DocWriter
 *
 * This class implements the CLI interface to the documentation writer.
 *
 * "Documentation writers" perform the following actions:
 *     1. Extract information from all the API's entry points.
 *     2. Organize the information previously extracted.
 *
 * Please not that this class only handles CLI options.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\DocWriter
 */

class DocWriter extends Command {

    // -----------------------------------------------------------------------------------------------------------------
    // Specific methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Build the "documentation writer".
     *
     * Please note that all "configuration writers" share some common configuration's parameters:
     *    * \dbeurive\Backend\Database\Doc\ConfigurationParameter::DOC_PATH
     *    * \dbeurive\Backend\Database\Doc\ConfigurationParameter::SCHEMA_PATH
     *    * \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::SQL_BASE_NS
     *    * \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::PROC_BASE_NS
     *    * \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::SQL_REPO_PATH
     *    * \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::PROC_REPO_PATH
     *    * \dbeurive\Backend\Cli\Option::CONFIG_LOADER_CLASS_NAME
     *
     * @see \dbeurive\Backend\Database\Doc\ConfigurationParameter::DOC_PATH
     * @see \dbeurive\Backend\Database\Doc\ConfigurationParameter::SCHEMA_PATH
     * @see \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::SQL_BASE_NS
     * @see \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::PROC_BASE_NS
     * @see \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::SQL_REPO_PATH
     * @see \dbeurive\Backend\Database\EntryPoints\ConfigurationParameter::PROC_REPO_PATH
     * @see \dbeurive\Backend\Cli\Option::CONFIG_LOADER_CLASS_NAME
     */
    final public function __construct() {
        parent::__construct();
        $this->addOption(DocOption::DOC_PATH,                    null, InputOption::VALUE_OPTIONAL, 'Path to the SQLite database used to store the documentation', 'sqldoc')
             ->addOption(DocOption::SCHEMA_PATH,                 null, InputOption::VALUE_REQUIRED, 'Path to the PHP file used to store the list of tables and fields within the database')
             ->addOption(EntryPointOption::SQL_BASE_NS,          null, InputOption::VALUE_REQUIRED, 'Base namespace for all the SQL requests')
             ->addOption(EntryPointOption::PROC_BASE_NS,         null, InputOption::VALUE_REQUIRED, 'Base namespace for all the procedures')
             ->addOption(EntryPointOption::SQL_REPO_PATH,        null, InputOption::VALUE_REQUIRED, 'Path to the directory that contains all the SQL requests')
             ->addOption(EntryPointOption::PROC_REPO_PATH,       null, InputOption::VALUE_REQUIRED, 'Path to the directory that contains all the procedures')
             ->addOption(CliOption::CONFIG_LOADER_CLASS_NAME,    null, InputOption::VALUE_OPTIONAL, 'Fully qualified name of a class used to load the configuration from a source.');

        // Options for the specific database' adapter will be added by the (child) class that handles the specific database' adapter.
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command
     */
    protected function configure() {
        $this->setName('db:doc-writer')
            ->setDescription("Build the documentation for all SQL requests and procedures.");
    }

    /**
     * This method is called by the Symfony's console class.
     * It executes the (specific: MySql, PostgreSql...) CLI adapter.
     * @see Symfony\Component\Console\Command\Command
     * @param InputInterface $input Input interface.
     * @param OutputInterface $output Output interface.
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        // Load the configuration from a file, if required.
        $configLoaderClass = $input->getOption(CliOption::CONFIG_LOADER_CLASS_NAME);

        $genericOptions = []; // Not required in PHP... but is sucks otherwise.

        if (! is_null($configLoaderClass)) {

            /** @var \dbeurive\Backend\Cli\InterfaceConfigLoader $loader */
            $loader = new $configLoaderClass();
            $genericOptions = $loader->load();
        } else {
            // The following options contains data used to use the API's entry points.
            $genericOptions = [
                DocOption::SCHEMA_PATH             => $input->getOption(DocOption::SCHEMA_PATH),
                DocOption::DOC_PATH                => $input->getOption(DocOption::DOC_PATH),
                EntryPointOption::SQL_BASE_NS      => $input->getOption(EntryPointOption::SQL_BASE_NS),
                EntryPointOption::PROC_BASE_NS     => $input->getOption(EntryPointOption::PROC_BASE_NS),
                EntryPointOption::SQL_REPO_PATH    => $input->getOption(EntryPointOption::SQL_REPO_PATH),
                EntryPointOption::PROC_REPO_PATH   => $input->getOption(EntryPointOption::PROC_REPO_PATH)
            ];
        }

        // Check the configuration.
        //   1. The configuration for the specific CLI adapter (MySql...) that will be used to extract the database' structure.
        //   2. The configuration for the documentation's builder (Writer).
        $status = Writer::checkConfiguration($genericOptions);
        if (count($status) > 0) {
            CliWriter::echoError(implode("\n", $status));
            return false;
        }

        // Execute the doc builder.
        Writer::writer($genericOptions);
    }
}