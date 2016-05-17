<?php

namespace dbeurive\BackendTest;

use dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Adapter\Database\Connector\ConfigurationParameter as ConnectorParameter;


/**
 * Class SetUp
 * @package dbeurive\BackendTest
 */

trait SetUp
{
    // -----------------------------------------------------------------------------------------------------------------
    // Application
    // -----------------------------------------------------------------------------------------------------------------

    /** @var DatabaseInterface */
    private $__di;
    /** @var array */
    private $__generalConfiguration;
    /** @var array Configuration for the application */
    private $__applicationConfiguration;

    // -----------------------------------------------------------------------------------------------------------------
    // MySql
    // -----------------------------------------------------------------------------------------------------------------

    /** @var \PDO */
    private $__mySqlPdo = null;
    /** @var array Configuration for the MySql database handler */
    private $__mySqlConfiguration;
    /** @var array Configuration for the MySql connector */
    private $__mySqlConnectorConfiguration;
    /** @var \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo MySql connector */
    private $__mySqlConnector;

    // -----------------------------------------------------------------------------------------------------------------
    // General initialization
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Initialize the tests.
     */
    public function __init() {
        $this->__generalConfiguration = require __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        $this->__mySqlConfiguration = $this->__generalConfiguration['mysql'];
        $this->__applicationConfiguration = $this->__generalConfiguration['application'];
        $this->__mySqlConnectorConfiguration = $this->__generalConfiguration['mysql-connector'][ConnectorParameter::CONNECTOR_CONFIG];
    }

    /**
     * Create the MSql connector.
     * Please call __init(), __createMySqlPdo() and __createMySqlDatabase() first.
     */
    private function __createMySqlConnector()
    {
        $this->__mySqlConnector = new \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo($this->__mySqlConnectorConfiguration);
    }

    /**
     * Create the database interface.
     * Please note that the created database interface is not fully initialized.
     * The database connector is not injected into the database interface.
     */
    public function __createDatabaseInterface() {

        $this->__init();

        // -------------------------------------------------------------------------------------------------------------
        // Get the backend's database's interface.
        //
        // See: \dbeurive\Backend\Database\DatabaseInterface::getInstance()
        // To initialize the database's interface, we need the following data:
        //
        //          use dbeurive\Backend\Database\EntryPoints\ConfigurationParameter
        //          use dbeurive\Backend\Database\Doc\ConfigurationParameter
        //
        //          EntryPointOption::SQL_REPO_PATH
        //          EntryPointOption::SQL_BASE_NS
        //          EntryPointOption::PROC_REPO_PATH
        //          EntryPointOption::PROC_BASE_NS
        //          DocOption::SCHEMA_PATH
        //
        //  Please note that all these configuration parameters may be set through the use of mutators once the instance
        //  is created.
        // -------------------------------------------------------------------------------------------------------------

        $this->__di = \dbeurive\Backend\Database\DatabaseInterface::getInstance('default', $this->__applicationConfiguration);
    }


    // -----------------------------------------------------------------------------------------------------------------
    // MySql initialization
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Create and return the PDO handler for MySql.
     * Please call __init() first.
     * @return \PDO
     */
    private function __createMySqlPdo() {
        if (is_null($this->__mySqlPdo)) {
            $this->__init();
            $dsn = "mysql:host=" . $this->__mySqlConfiguration[MySqlPdo::DB_HOST] . ";port=" . $this->__mySqlConfiguration[MySqlPdo::DB_PORT];
            $this->__mySqlPdo = new \PDO($dsn, $this->__mySqlConfiguration[MySqlPdo::DB_USER], $this->__mySqlConfiguration[MySqlPdo::DB_PASSWORD], []);
        }
        return $this->__mySqlPdo;
    }

    /**
     * Create the MSql database.
     * Please call __init() and __createMySqlPdo() first.
     */
    private function __createMySqlDatabase()
    {
        // -------------------------------------------------------------------------------------------------------------
        // Load the configuration and open a connection to the database.
        // -------------------------------------------------------------------------------------------------------------

        $schema = require $this->__generalConfiguration['test']['dir.fixtures'] . DIRECTORY_SEPARATOR . 'MySql' . DIRECTORY_SEPARATOR . 'schema.php';

        // -------------------------------------------------------------------------------------------------------------
        // Drop the database, then re-create.
        // -------------------------------------------------------------------------------------------------------------

        foreach ($schema as $_request) {
            $req = $this->__mySqlPdo->prepare($_request);
            if (false === $req->execute([])) {
                throw new \Exception("Can not create the database.");
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Load data into the database.
        // -------------------------------------------------------------------------------------------------------------

        $dataPath = $this->__generalConfiguration['test']['dir.fixtures'] . DIRECTORY_SEPARATOR . 'MySql' . DIRECTORY_SEPARATOR . 'data.php';
        \dbeurive\Util\UtilCode::require_with_args($dataPath, ['pdo' => $this->__mySqlPdo]);
    }
}