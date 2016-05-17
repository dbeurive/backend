<?php

/**
 * This file implements the "database interface".
 * See the description for the class.
 */

namespace dbeurive\Backend\Database;
use dbeurive\Backend\Database\Entrypoints\Provider as EntryPointProvider;
use dbeurive\Backend\Database\Connector\AbstractConnector;
use dbeurive\Backend\Database\Entrypoints\ConfigurationParameter as EntryPointOption;
use dbeurive\Backend\Database\Doc\ConfigurationParameter as DocOption;


/**
 * Class DatabaseInterface
 *
 * This class implements the "database interface".
 *
 * The "database interface" provides:
 *   * access to the database.
 *     Through this interface the application gain access to API's entry points (SQL requests and procedures).
 *   * information about the database.
 *     For example, an API's entry point may request the list of fields that compose a given table.
 *
 * @package dbeurive\Backend\Database
 */

class DatabaseInterface {

    /**
     * @var array List of created data interfaces.
     *      [<name> => <instance>, <name> => <instance>...]
     */
    private static $__interfacesRepository = [];
    /**
     * @var string Name of this interface.
     */
    private $__name = null;
    /**
     * @var AbstractConnector Handler to the database connector.
     *      Note: this property is used when the application is running.
     * @see setDbConnector
     */
    private $__connector = null;
    /**
     * @var EntryPointProvider Entry point provider.
     *      The entry point provider is created, and configured, during the creation of this database interface (see method `getInstance()`).
     * @see __construct
     * @see getInstance
     */
    private $__entryPointProvider = null;
    /**
     * @var string Path to the PHP file that contains the list of tables and field within the database.
     */
    private $__phpDbRepresentationPath = null;
    /**
     * @var array List of all tables and fields within the database.
     *      Structure of this array: array( <table name> => array(<field name>, <field name>,...),
     *                                      <table name> => array(<field name>, <field name>,...),
     *                                      ...
     *                               )
     */
    private $__databaseSchema = null;



    /**
     * Create a new data interface.
     * @param string $inName Name od this interface.
     */
    private function __construct($inName) {
        $this->__entryPointProvider = new EntryPointProvider($inName);
        $this->__name = $inName;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters for the class.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Create a new data interface ot get an existing one.
     *
     * @param string $inName Name of the interface to create or to get.
     * @param array|null $inOptConfig Optional configuration.
     *        Configuration parameters are:
     *
     *        Mandatory (these parameters may also be set by using mutators):
     *          EntryPointOption::SQL_REPO_PATH
     *          EntryPointOption::SQL_BASE_NS
     *          EntryPointOption::PROC_REPO_PATH
     *          EntryPointOption::PROC_BASE_NS
     *          DocOption::SCHEMA_PATH
     *
     *        Optional:
     *          EntryPointOption::DB_CONNECTOR (used only when the application is running)
     *
     * @return DatabaseInterface The method returns a new data interface.
     *
     * @see EntryPointOption::SQL_REPO_PATH
     * @see EntryPointOption::SQL_BASE_NS
     * @see EntryPointOption::PROC_REPO_PATH
     * @see EntryPointOption::PROC_BASE_NS
     * @see DocOption::SCHEMA_PATH
     * @see EntryPointOption::DB_CONNECTOR
     * @see setDbConnector
     */
    static public function getInstance($inName='default', array $inOptConfig=null) {

        if (array_key_exists($inName, self::$__interfacesRepository)) {
            return self::$__interfacesRepository[$inName];
        }
        $di = new DatabaseInterface($inName); // This also create an entry point provider.

        if (! is_null($inOptConfig)) {
            // The following lines configure the entry point provider.
            $di->setSqlRepositoryBasePath($inOptConfig[EntryPointOption::SQL_REPO_PATH]);
            $di->setSqlBaseNameSpace($inOptConfig[EntryPointOption::SQL_BASE_NS]);
            $di->setProcedureRepositoryBasePath($inOptConfig[EntryPointOption::PROC_REPO_PATH]);
            $di->setProcedureBaseNameSpace($inOptConfig[EntryPointOption::PROC_BASE_NS]);
            $di->setPhpDatabaseRepresentationPath($inOptConfig[DocOption::SCHEMA_PATH]);

            if (array_key_exists(EntryPointOption::DB_CONNECTOR, $inOptConfig)) {
                /** @var \dbeurive\Backend\Database\Connector\AbstractConnector $c */
                $c = $inOptConfig[EntryPointOption::DB_CONNECTOR];
                $di->setDbConnector($c);
            }
        }

        self::$__interfacesRepository[$inName] = $di;
        return self::$__interfacesRepository[$inName];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Setters for the instance.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the database handler.
     * @param AbstractConnector $inConnector Handler to the database connector.
     */
    public function setDbConnector(AbstractConnector $inConnector) {
        $this->__connector = $inConnector;
    }

    /**
     * Set the path to the PHP file that contains the list of tables and field within the database.
     * If the file exists, then the schema of the database is loaded from the file.
     * @param string $inPath Path to the PHP file that contains the list of tables and field within the database.
     * @return bool If the schema has been loaded, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    public function setPhpDatabaseRepresentationPath($inPath) {
        $this->__phpDbRepresentationPath = $inPath;
        if (file_exists($inPath)) {
            self::setDatabaseSchema(require $inPath);
            return true;
        }
        return false;
    }

    /**
     * Set the database's schema.
     * @param array $inDescription The database description.
     *        Structure of this array: array( <table name> => array(<field name>, <field name>,...),
     *                                        <table name> => array(<field name>, <field name>,...),
     *                                        ...
     *                                 )
     *        Please note that this value should come from the call to "ServiceProvider::getPhpDatabaseRepresentation()".
     */
    public function setDatabaseSchema(array $inDescription) {
        $this->__databaseSchema = $inDescription;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters for the instance.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the handler to the database connector.
     * @return AbstractConnector The method returns the handler to the database connector.
     */
    public function getDbConnector() {
        if (is_null($this->__connector)) {
            throw new \Exception("You did not set the database connector! Please call setDbConnector() first!");
        }

        return $this->__connector;
    }

    /**
     * Return the entry points' provider.
     * @return EntryPointProvider
     */
    public function getEntryPointProvider() {
        return $this->__entryPointProvider;
    }

    /**
     * Return the database' schema.
     * @return array The method returns the schema of the database.
     *         Please see the description for the property $__databaseSchema.
     * @throws \Exception
     */
    public function getDatabaseSchema() {

        if (is_null($this->__databaseSchema)) {
            $path = $this->__phpDbRepresentationPath;
            $path = is_null($path) ? "The path to the database's representation has not been set!" : "The path to the database's representation should be: " . $path;
            $message = "The schema of the database has not been set.\nPlease call the method \"setDatabaseSchema()\" or \"setPhpDatabaseRepresentationPath()\" first.\n${path}";
            throw new \Exception($message);
        }

        return $this->__databaseSchema;
    }

    /**
     * Get all fields' names within a given table.
     * @param string $inTableName Name of the table.
     * @return array The method returns the list of fields within the table.
     * @throws \Exception
     */
    public function getTableFieldsNames($inTableName) {

        $databaseSchema = $this->getDatabaseSchema(); // Perform some sanity checks.

        if (! array_key_exists($inTableName, $databaseSchema)) {
            throw new \Exception("The table \"$inTableName\" does not exist!");
        }

        return $databaseSchema[$inTableName];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Configuration for the entry point provider.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the base namespace for the SQL requests.
     * @param string $inNameSpace The namespace to set.
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function setSqlBaseNameSpace($inNameSpace) {
        $this->__entryPointProvider->setSqlBaseNameSpace($inNameSpace);
    }

    /**
     * Set the base namespace for the database procedures.
     * @param string $inNameSpace The namespace to set.
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function setProcedureBaseNameSpace($inNameSpace) {
        $this->__entryPointProvider->setProcedureBaseNameSpace($inNameSpace);
    }

    /**
     * Set the path to the directory used to store the SQL requests' definitions.
     * @param string $inPath Path to the directory used to store the SQL requests' definitions.
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function setSqlRepositoryBasePath($inPath) {
        $this->__entryPointProvider->setSqlRepositoryBasePath($inPath);
    }

    /**
     * Set the path to the directory used to store all the procedures' definitions.
     * @param string $inPath Path to the directory used to store the procedures' definitions.
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function setProcedureRepositoryBasePath($inPath) {
        $this->__entryPointProvider->setProcedureRepositoryBasePath($inPath);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Get data from the entry point provider.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns the list of all documentations for SQL requests.
     * @return array The method returns an array that contains all documentations for SQL requests.
     *         Elements' type is: \dbeurive\Backend\Database\Entrypoints\Description\Sql
     * @throws \Exception
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function getAllSqlDescriptions() {
        return $this->__entryPointProvider->getAllSqlDescriptions();
    }

    /**
     * Returns the list of all documentations for the database procedure.
     * @return array The method returns an array that contains all documentations for database procedures.
     *         Elements' type is: \dbeurive\Backend\Database\Entrypoints\Description\Procedure
     * @throws \Exception
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function getAllProceduresDescriptions() {
        return $this->__entryPointProvider->getAllProceduresDescriptions();
    }

    /**
     * Return the path to the SQL repository.
     * @return string The method returns the path to the SQL repository.
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function getSqlRepositoryBasePath() {
        return $this->__entryPointProvider->getSqlRepositoryBasePath();
    }

    /**
     * Return the path to the procedure repository.
     * @return string The method returns the path to the procedure repository.
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function getProcedureRepositoryBasePath() {
        return $this->__entryPointProvider->getProcedureRepositoryBasePath();
    }

    /**
     * Return an SQL request identified by its name.
     * @param string $inName Name of the SQL request.
     * @return \dbeurive\Backend\Database\Entrypoints\AbstractSql
     * @throws \Exception
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     * @note The method should not be called from the application.
     *       It has been introduced for the unit tests.
     */
    public function getSql($inName) {
        /** @var \dbeurive\Backend\Database\Entrypoints\AbstractSql $sql */
        $sql = $this->__entryPointProvider->getSql($inName);
        $sql->setDbh($this->__connector->getDatabaseHandler());
        $sql->setFieldsProvider(function($inName) { return $this->getTableFieldsNames($inName); } );
        return $sql;
    }

    /**
     * Return a procedure identified by its name.
     * @param string $inName Name of the procedure.
     * @return \dbeurive\Backend\Database\Entrypoints\AbstractSql
     * @throws \Exception
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function getProcedure($inName) {
        /** @var \dbeurive\Backend\Database\Entrypoints\AbstractProcedure $procedure */
        $procedure = $this->__entryPointProvider->getProcedure($inName);
        $procedure->setSqlProvider(function($inName) { return $this->getSql($inName); });
        return $procedure;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // The following functions have been added because they make the tests' suite easier.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the database handler. Typically, this can be an instance of the \PDO class.
     * @return mixed The method returns the database handler (example: an instance of \PDO).
     * @throws \Exception
     */
    public function getDatabaseHandler() {
        return $this->getDbConnector()->getDatabaseHandler();
    }

}