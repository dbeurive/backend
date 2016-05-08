<?php

/**
 * This file implements the "database interface".
 * See the description for the class.
 */

namespace dbeurive\Backend\Database;
use dbeurive\Backend\Database\Entrypoints\Provider as EntryPointProvider;
use dbeurive\Backend\Database\Link\AbstractLink;
use dbeurive\Backend\Database\Entrypoints\Option as EntryPointOption;
use dbeurive\Backend\Database\Doc\Option as DocOption;


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
     * This constant defines an optional flag that specifies that we want to get a list of fields as an array of "relative names".
     * Note: "Relative names" means "relative to a given table".
     * Example for the table "user": ['id', 'login', 'password']
     */
    const FIELDS_RAW_AS_ARRAY = 1;
    /**
     * This constant defines an optional flag that specifies that we want to get a list of fields as an array of "fully qualified names".
     * Note: "Fully qualified names" include the table's name.
     * Example for the table "user": ['user.id', 'user.login', 'user.password']
     */
    const FIELDS_FULLY_QUALIFIED_AS_ARRAY = 2;
    /**
     * This constant defines an optional flag that specifies that we want to get a list of fields as a ready to use SQL chunk.
     * Example for the table "user": `user`.`id` AS 'user.id', `user`.`login` AS 'user.login', `user`.`password` AS 'user.password'.
     */
    const FIELDS_FULLY_QUALIFIED_AS_SQL = 3;


    /**
     * @var array List of created data interfaces.
     */
    private static $__repository = [];

    /**
     * @var string Name of this interface.
     */
    private $__name = null;

    /**
     * @var AbstractLink Database link.
     */
    private $__link = null;

    /**
     * @var EntryPointProvider Entry point provider.
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
     * @param string $inName Name of the interface to create or to get.
     * @param array|null $inOptConfig Optional configuration.
     *        Options are:
     *        use dbeurive\Backend\Database\Entrypoints\Option as EntryPointOption;
     *        use dbeurive\Backend\Database\Doc\Option as DocOption;
     *        EntryPointOption::SQL_REPO_PATH
     *        EntryPointOption::SQL_BASE_NS
     *        EntryPointOption::PROC_REPO_PATH
     *        EntryPointOption::PROC_BASE_NS
     *        DocOption::PHP_DB_DESC_PATH
     * @return DatabaseInterface The method returns a new data interface.
     */
    static public function getInstance($inName='default', array $inOptConfig=null) {

        if (array_key_exists($inName, self::$__repository)) {
            return self::$__repository[$inName];
        }
        $di = new DatabaseInterface($inName);

        if (! is_null($inOptConfig)) {
            $di->setSqlRepositoryBasePath($inOptConfig[EntryPointOption::SQL_REPO_PATH]);
            $di->setSqlBaseNameSpace($inOptConfig[EntryPointOption::SQL_BASE_NS]);
            $di->setProcedureRepositoryBasePath($inOptConfig[EntryPointOption::PROC_REPO_PATH]);
            $di->setProcedureBaseNameSpace($inOptConfig[EntryPointOption::PROC_BASE_NS]);
            $di->setPhpDatabaseRepresentationPath($inOptConfig[DocOption::PHP_DB_DESC_PATH]);

            if (array_key_exists(EntryPointOption::DB_LINK, $inOptConfig)) {
                $di->setDbLink($inOptConfig[EntryPointOption::DB_LINK]);
            }
        }

        self::$__repository[$inName] = $di;
        return self::$__repository[$inName];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Setters for the instance.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the database handler.
     * @param AbstractLink $inLink Database link.
     */
    public function setDbLink(AbstractLink $inLink) {
        $this->__link = $inLink;
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
     * Return the handler to the relational database management system.
     * @return AbstractLink The method returns the relational database management system.
     */
    public function getDbLink() {
        if (is_null($this->__link)) {
            throw new \Exception("You did not set the link to the relational database management system! Please call setDbLink() first!");
        }

        return $this->__link;
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
     * @param int $inOptFormat This parameter specifies the output format. Value can be:
     *        * self::FIELDS_RAW_AS_ARRAY: the list of fields' names is returned as an array of strings.
     *                                     Each element of the returned array is a string "<field name>".
     *        * self::FIELDS_FULLY_QUALIFIED_AS_ARRAY: the list of fully qualified fields' names is returned as an array of strings.
     *                                     Each element of the returned array is a string "<table name>.<field name>".
     *        * self::FIELDS_FULLY_QUALIFIED_AS_SQL: the list of fields' names is returned as a string "<table name>.<field name> as '<table name>.<field name>',...".
     * @param bool $inOptQuote This parameter indicates whether we should quote the fields' names or not.
     *        For example, with MySql, you can quote "user.id" into "`user`.`id`".
     * @return array The method returns the list of fields within the table.
     * @throws \Exception
     */
    public function getTableFieldsNames($inTableName, $inOptFormat=self::FIELDS_RAW_AS_ARRAY, $inOptQuote=true) {

        $quoter = function($e) { return $e; };

        if ($inOptQuote) {

            if (is_null($this->__link)) {
                throw new \Exception("In order to quote fields names according to the database server brand name, you need a valid instance of the link.");
            }

            $link = $this->__link;
            $quoter = function ($fieldName) use ($link) {
                return $link->quoteFieldName($fieldName);
            };
        }

        $databaseSchema = $this->getDatabaseSchema(); // Perform some sanity checks.
        if (is_null($quoter)) {
            $quoter = function($inFieldName) { return $inFieldName; };
        }

        if (! array_key_exists($inTableName, $databaseSchema)) {
            throw new \Exception("The table \"$inTableName\" does not exist!");
        }

        switch ($inOptFormat) {
            case self::FIELDS_RAW_AS_ARRAY:              return $databaseSchema[$inTableName];
            case self::FIELDS_FULLY_QUALIFIED_AS_ARRAY:  return array_map($quoter, self::__getFullyQualifiedTableFieldsNames($inTableName, $databaseSchema));
            case self::FIELDS_FULLY_QUALIFIED_AS_SQL:    return implode(', ', array_map(function($e) use($quoter) { return "{$quoter($e)} as '${e}'"; }, self::__getFullyQualifiedTableFieldsNames($inTableName, $databaseSchema)));
        }

        throw new \Exception("Invalid format specifier (${inOptFormat}) for the list of fields on table \"${inTableName}\".");
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
     * @param array $inInitConfig Configuration for the SQL request's construction.
     * @param array $inExecutionConfig Execution configuration for the SQL request.
     *        Typically, this array contains the values of the fields required by the request's execution.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication
     * @throws \Exception
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     * @note The method should not be called from the application.
     *       It has been introduced for the unit tests.
     */
    public function getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null) {
        return $this->__entryPointProvider->getSql($inName, $inInitConfig, $inExecutionConfig);
    }

    /**
     * Return a procedure identified by its name.
     * @param string $inName Name of the procedure.
     * @param array $inInitConfig Configuration for the procedure's construction.
     * @param array $inExecutionConfig Execution configuration for the procedure.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication
     * @throws \Exception
     * @see dbeurive\Backend\Database\Entrypoints\Provider
     */
    public function getProcedure($inName, array $inInitConfig = [], array $inExecutionConfig = null) {
        return $this->__entryPointProvider->getProcedure($inName, $inInitConfig, $inExecutionConfig);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Private functions
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Get all the fields' names within a given table, as a list of fully qualified names "<table name>.<field name>".
     * @param string $inTableName Name of the table.
     * @param array $inDatabaseSchema This array represents the database' schema.
     * @return array The method returns the list of fully qualified fields' names.
     * @throws \Exception
     */
    private static function __getFullyQualifiedTableFieldsNames($inTableName, array $inDatabaseSchema) {
        if (! array_key_exists($inTableName, $inDatabaseSchema)) {
            throw new \Exception("The table ${inTableName} is unkonwn.");
        }
        return array_map(function($e) use ($inTableName) { return $inTableName . '.' . $e; }, $inDatabaseSchema[$inTableName]);
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
        return $this->getDbLink()->getDatabaseConnexionHandler();
    }

}