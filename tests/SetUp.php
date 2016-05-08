<?php

namespace dbeurive\BackendTest;

use dbeurive\Backend\Database\Link\Option as LinkOption;
use dbeurive\Backend\Database\Link\MySql;
use dbeurive\Backend\Database\DatabaseInterface;


/**
 * Class SetUp
 */

trait SetUp
{
    /** @var DatabaseInterface */
    private $__di;
    /** @var array */
    private $__generalConfiguration;
    /** @var array */
    private $__linkConfiguration;
    /** @var \PDO */
    private $__pdo;
    /** @var \dbeurive\Backend\Database\Link\AbstractLink */
    private $__link;

    /**
     * Load the configuration for the tests.
     * @return array
     */
    private function __loadConfig() {
        return require __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
    }

    /**
     * Initialize the tests.
     */
    public function __init() {
        $this->__generalConfiguration = $this->__loadConfig();
    }

    /**
     *
     * @throws \Exception
     */
    private function __createMysqlDatabase()
    {
        $this->__init();

        // -------------------------------------------------------------------------------------------------------------
        // Load the configuration and open a connection to the database.
        // -------------------------------------------------------------------------------------------------------------

        $schema = require $this->__generalConfiguration['test']['dir.fixtures'] . DIRECTORY_SEPARATOR . 'MySql' . DIRECTORY_SEPARATOR . 'schema.php';
        $this->__linkConfiguration = $this->__generalConfiguration['mysql'][LinkOption::LINK_CONFIG];
        $dsn = "mysql:host=" . $this->__linkConfiguration[MySql::DB_HOST] . ";port=" . $this->__linkConfiguration[MySql::DB_PORT];
        $this->__pdo = new \PDO($dsn, $this->__linkConfiguration[MySql::DB_USER], $this->__linkConfiguration[MySql::DB_PASSWORD], []);

        // -------------------------------------------------------------------------------------------------------------
        // Drop the database, then re-create.
        // -------------------------------------------------------------------------------------------------------------

        foreach ($schema as $_request) {
            $req = $this->__pdo->prepare($_request);
            if (false === $req->execute([])) {
                throw new \Exception("Can not create the database.");
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Load data into the database.
        // -------------------------------------------------------------------------------------------------------------

        $dataPath = $this->__generalConfiguration['test']['dir.fixtures'] . DIRECTORY_SEPARATOR . 'MySql' . DIRECTORY_SEPARATOR . 'data.php';
        \dbeurive\Util\UtilCode::require_with_args($dataPath, ['pdo' => $this->__pdo]);

    }

    /**
     * Create the link to the database from a given database brand name.
     * @param string $inDbName Database brand name ("mysql").
     * @param bool $inOptConnect This flag indicates whether the link should open a connection to the database or not.
     *        * This the value of this parameter is true, then the link is created, and the connexion to the database is established.
     *        * Otherwise, the link is created, but the connexion to the database is not established.
     * @throws \Exception
     */
    public function __createLink($inDbName, $inOptConnect=false)
    {
        $this->__init();

        // -------------------------------------------------------------------------------------------------------------
        // Initialise the link to the database.
        // -------------------------------------------------------------------------------------------------------------

        $linkType = $this->__generalConfiguration[$inDbName][LinkOption::LINK_NAME];
        $linkConf = $this->__generalConfiguration[$inDbName][LinkOption::LINK_CONFIG];
        /** @var \dbeurive\Backend\Database\Link\MySql $link */
        $this->__link = new $linkType();
        $errors = $this->__link->setConfiguration($linkConf);
        if (count($errors) > 0) {
            throw new \Exception("Invalid configuration: " . implode(", ", $errors));
        }
        if ($inOptConnect) {
            $this->__link->connect();
        }
    }

    /**
     * Create the database interface.
     * Please note that the created database interface is not fully initialized.
     * The database link ($this->__link) is not injected into the database interface.
     */
    public function __createDatabaseInterface() {

        $this->__init();

        // -------------------------------------------------------------------------------------------------------------
        // Get the backend's database's interface.
        //
        // See: \dbeurive\Backend\Database\DatabaseInterface::getInstance()
        // To initialize the database's interface, we need the following data:
        //
        //          use dbeurive\Backend\Database\Entrypoints\Option as EntryPointOption;
        //          use dbeurive\Backend\Database\Doc\Option as DocOption;
        //
        //          EntryPointOption::SQL_REPO_PATH
        //          EntryPointOption::SQL_BASE_NS
        //          EntryPointOption::PROC_REPO_PATH
        //          EntryPointOption::PROC_BASE_NS
        //          DocOption::PHP_DB_DESC_PATH
        //
        //  Please note that all these configuration parameters may be set through the use of mutators once the instance
        //  is created.
        // -------------------------------------------------------------------------------------------------------------

        $this->__di = \dbeurive\Backend\Database\DatabaseInterface::getInstance('default', $this->__generalConfiguration['application']);
    }

}