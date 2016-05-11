<?php

/**
 * This file implements the base class for all API's entry points.
 * An API's entry point can be:
 *    * an SQL request
 *    * a procedure
 */

namespace dbeurive\Backend\Database\Entrypoints\Application;
use dbeurive\Backend\Database\Connector\AbstractConnector;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Database\Entrypoints\Provider;
use dbeurive\Backend\Database\SqlService\InterfaceSqlService as SqlService;

/**
 * Class AbstractEntryPoint
 *
 * This class is the base class for all API's entry points.
 * An API's entry point can be:
 *    * an SQL request
 *    * a procedure
 *
 * @package dbeurive\Backend\Database\Entrypoints\Application
 */

abstract class AbstractApplication {

    /**
     * @see \dbeurive\Backend\Database\DatabaseInterface
     */
    const FIELDS_RAW_AS_ARRAY = DatabaseInterface::FIELDS_RAW_AS_ARRAY;
    /**
     * @see \dbeurive\Backend\Database\DatabaseInterface
     */
    const FIELDS_FULLY_QUALIFIED_AS_ARRAY = DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_ARRAY;
    /**
     * @see \dbeurive\Backend\Database\DatabaseInterface
     */
    const FIELDS_FULLY_QUALIFIED_AS_SQL = DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_SQL;

    /**
     * @var \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result|\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result Result for the last execution.
     */
    protected $_result = null;
    /**
     * @var array Configuration for the API's entry point execution.
     *      The structure of this variable depends on the type of API's entry point (SQL request or procedure).
     *      Although it is possible to define a structure for procedures' configuration, it's not the case for SQL requests.
     *      SQL requests' organisations may be complex (with sub selections).
     *      Thus, for SQL requests, the configuration's structure is free.
     */
    protected $_executionConfig = [];
    /**
     * @var AbstractConnector Handler to the database connector.
     */
    protected $_connector = null;
    /**
     * @var \dbeurive\Backend\Database\Entrypoints\Provider Entry point provider.
     *      This attribute has been introduced for the procedures (procedures need to get SQL requests).
     *      This attribute is used by the procedure to get SQL requests.
     */
    protected $_provider = null;
    /**
     * Indicates whether the API's entry point has been executed.
     * @var bool If the API's entry point has been executed, then the property's value is true.
     *      Otherwise, the property's value is false.
     */
    protected $_hasBeenExecuted = false;
    /**
     * @var null|\dbeurive\Backend\Database\SqlService\InterfaceSqlService SQL service provider.
     */
    private $__sqlService = null;

    /**
     * Create a new API's entry point.
     *
     * Please note that instantiation of entry points takes place in two contexts:
     *    * During the documentation process.
     *    * During the application's execution.
     *
     * **Documentation process**
     *
     * During the documentation process, no connexion to the database is established.
     * Therefore, in this context, no "database connector" is created (`$inOptConnector = null`).
     *
     * **Application's execution**
     *
     * During the application's execution, a connexion to the database is established.
     * Therefore, in this context, a "database connector" is created.
     *
     * @param Provider $inEntryPointProvider Entry point provider that handles this entry point.
     * @param null|AbstractConnector $inOptConnector Handler to the database connector.
     *        Please note that, depending on the context, this parameter is defined or not.
     * @param array $inOptInitConfig Configuration for the entry point's initialization.
     *
     * @uses \dbeurive\Backend\Database\Entrypoints\Provider::__getDescriptions
     * @uses \dbeurive\Backend\Database\Entrypoints\Provider::getSql
     * @uses \dbeurive\Backend\Database\Entrypoints\Provider::getProcedure
     */
    final public function __construct(Provider $inEntryPointProvider, AbstractConnector $inOptConnector=null, array $inOptInitConfig=[]) {
        $this->_provider = $inEntryPointProvider;

        if (! is_null($inOptConnector)) {
            // The application is running.
            $this->_connector = $inOptConnector;
            $className = $inOptConnector->getSqlServiceProvider();
            $this->__sqlService = new $className();
        }
        $this->_init($inOptInitConfig);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Abstract methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Validate the configuration of the entry point prior to its execution.
     * @param array $inExecutionConfig Configuration to validate.
     * @param string $outErrorMessage Reference to a string used to store an error message, if an error occurs.
     * @return bool If the execution configuration is valid, then the method returns the value true.
     *         Otherwise, it returns the value false. In this case, the string `$outErrorMessage` should contain an eror message.
     */
    abstract protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage);

    /**
     * Execute the API's entry point.
     * @param array $inExecutionConfig Configuration for the execution.
     * @param AbstractConnector $inConnector Handler to the database connector.
     * @param SqlService $inSqlService SQL service provider.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result|\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result
     */
    abstract protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector, SqlService $inSqlService);

    /**
     * Initialize the API's entry point.
     * @param array $inConfig Entry point's configuration.
     */
    abstract protected function _init(array $inInitConfig=[]);

    /**
     * Return the description of the API's entry point.
     * @return \dbeurive\Backend\Database\Entrypoints\Description\Sql|\dbeurive\Backend\Database\Entrypoints\Description\Procedure
     *         The method returns the description of the API's entry point.
     */
    abstract public function getDescription();

    // -----------------------------------------------------------------------------------------------------------------
    // Protected methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the execution's configuration.
     * @param array $inExecutionConfig Configuration to set.
     * @return $this
     */
    protected function _setExecutionConfig(array $inExecutionConfig) {
        $this->_hasBeenExecuted = false;
        $this->_result = null;
        $this->_executionConfig = $inExecutionConfig;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the result of the last execution.
     * @return null|\dbeurive\Backend\Database\Entrypoints\Application\BaseResult Result of the last execution.
     *         If the API's entry point has never been executed, then the method returns the value null.
     */
    public function getResult() {
        return $this->_result; // May be null
    }

    /**
     * Return the "data sets" that result from the API's entry point execution.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *   * Fields' values returned by the SGBDR.
     *   * Calculated values returned by the SGBDR.
     * @return array The method returns data sets" that result from the API's entry point execution.
     * @throws \Exception
     */
    public function getDataSet() {
        return $this->getResult()->getDataSets();
    }

    /**
     * Get all fields' names within a given table.
     * @param string $inTableName Name of the table.
     * @param int $inOptFormat This parameter specifies the output format. Value can be:
     *        * \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication::FIELDS_RAW_AS_ARRAY: the list of fields' names is returned as an array of strings.
     *          Each element of the returned array is a string "<field name>".
     *        * \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication::FIELDS_FULLY_QUALIFIED_AS_ARRAY: the list of fully qualified fields' names is returned as an array of strings.
     *          Each element of the returned array is a string "<table name>.<field name>".
     *        * \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication::FIELDS_FULLY_QUALIFIED_AS_SQL: the list of fields' names is returned as a string "<table name>.<field name> as '<table name>.<field name>',...".
     * @param bool $inOptQuote This parameter indicates whether we should quote the fields' names or not.
     *        For example, with MySql, you can quote "user.id" into "`user`.`id`".
     * @return array The method returns the list of fields within the table.
     * @throws \Exception
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication::FIELDS_RAW_AS_ARRAY
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication::FIELDS_FULLY_QUALIFIED_AS_ARRAY
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication::FIELDS_FULLY_QUALIFIED_AS_SQL
     */
    protected function _getTableFieldsNames($inTableName, $inFormat=self::FIELDS_RAW_AS_ARRAY, $inOptQuote=true) {
        return $this->_provider->getDataInterface()->getTableFieldsNames($inTableName, $inFormat, $inOptQuote);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Public methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Reset the execution's configuration.
     * @return $this
     */
    public function resetExecutionConfig() {
        $this->_executionConfig = [];
        return $this;
    }

    /**
     * Execute the API's entry point.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result|\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result The method returns the result of the execution.
     * @throws \Exception
     */
    public function execute()
    {
        $outErrorMessage = null;
        if (! $this->_validateExecutionConfig($this->_executionConfig, $outErrorMessage)) {
            throw new \Exception("The configuration for the API's execution is not valid.\n${outErrorMessage}\nGiven configuration is: " . print_r($this->_executionConfig, true));
        }

        $this->_hasBeenExecuted = true;
        $this->_result = null;
        $this->_result = $this->_execute($this->_executionConfig, $this->_connector, $this->__sqlService);
        return $this->_result;
    }

    /**
     * Test if the request has been successfully executed.
     * @return bool If the request has been successfully executed, then the method returns the value true.
     *         Otherwise, it returns the value false.
     * @throws \Exception
     */
    public function isSuccess() {
        $result = $this->getResult();
        if (is_null($result)) {
            throw new \Exception("You try to get the status of an action that has not been executed.");
        }
        return $result->isSuccess();
    }

    /**
     * Test if the request failed due to an error.
     * @return bool If the request failed due to en error, then the method returns the value true.
     *         Otherwise, it returns the value false.
     * @throws \Exception
     */
    public function isError() {
        $result = $this->getResult();
        if (is_null($result)) {
            throw new \Exception("You try to get the status of an action that has not been executed.");
        }
        return $result->isError();
    }

    /**
     * Test if the execution of the API's entry point returned at least one data set.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *   * Fields' values returned by the SGBDR.
     *   * Calculated values returned by the SGBDR..
     * @return bool If the execution of the API's entry point returned at least one data set, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     */
    public function isDataSetEmpty() {
        $result = $this->getResult();
        if (is_null($result)) {
            throw new \Exception("You try to get the status of an action that has not been executed.");
        }
        return $result->isDataSetsEmpty();
    }
    
    /**
     * Test whether the API's entry point has been executed or not.
     * @return bool Il the PI's entry point has been executed, then the function returns the value true.
     *         Otherwise, it returns the value false.
     */
    public function hasBeenExecuted() {
        return $this->_hasBeenExecuted;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Protected methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Convert a configuration array into string.
     * @param array $inConfig Configuration array.
     * @return string The string that represents the configuration.
     */
    protected function _confToString(array $inConfig) {
        return json_encode($inConfig);
    }

    /**
     * Return the execution's configuration.
     * @return array
     */
    protected function _getConfig() {
        return $this->_executionConfig;
    }
}