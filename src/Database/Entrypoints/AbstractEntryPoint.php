<?php

/**
 * This file implements the base class for all API's entry points.
 */

namespace dbeurive\Backend\Database\Entrypoints;
use dbeurive\Backend\Database\Connector\AbstractConnector;
use dbeurive\Backend\Database\DatabaseInterface;

/**
 * Class AbstractEntryPoint
 *
 * This class is the base class for all API's entry points.
 * An API's entry point can be:
 *    * an SQL request
 *    * a procedure
 *
 * @package dbeurive\Backend\Database\Entrypoints
 */

abstract class AbstractEntryPoint {

    /**
     * @see dbeurive\Backend\Database\DatabaseInterface
     */
    const FIELDS_RAW_AS_ARRAY = DatabaseInterface::FIELDS_RAW_AS_ARRAY;
    /**
     * @see dbeurive\Backend\Database\DatabaseInterface
     */
    const FIELDS_FULLY_QUALIFIED_AS_ARRAY = DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_ARRAY;
    /**
     * @see dbeurive\Backend\Database\DatabaseInterface
     */
    const FIELDS_FULLY_QUALIFIED_AS_SQL = DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_SQL;

    /**
     * @var \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result|\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result Result for the last execution.
     */
    protected $_result = null;
    /**
     * @var array Configuration for the API's entry point execution.
     *      The structure of this variable depends on the type of the API's entry point (SQL request or procedure).
     *      Although it is possible to define a structure for procedures' configuration, it's not the case for SQL requests.
     *      SQL requests' organisations may be complex (with sub selections).
     *      Thus, for SQL requests, the configuration's structure is free.
     */
    protected $_execConfig = [];
    /**
     * @var AbstractConnector Handler to the database connector.
     */
    protected $_connector = null;
    /**
     * @var \dbeurive\Backend\Database\Entrypoints\Provider Entry point provider.
     *      This attribute has been introduced for the procedures.
     *      Procedures need to get SQL requests.
     */
    protected $_provider = null;



    /**
     * Create a new API's entry point.
     * @param null|AbstractConnector $inConnector Handler to the database connector.
     * @param Provider $inEntryPointProvider Entry point provider that handles this entry point.
     * @param array $inOptInitConfig Configuration for the entry point's initialization.
     */
    public function __construct(AbstractConnector $inConnector, Provider $inEntryPointProvider, array $inOptInitConfig=[]) {
        if (! is_null($inConnector)) {
            $this->_connector = $inConnector;
        }
        $this->_provider = $inEntryPointProvider;
        $this->_init($inOptInitConfig);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Abstract methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This method must be implemented by all API's entry points.
     * It validates the configuration prior to the execution.
     * @param string $outErrorMessage Reference to a string used to store an error message, if an error occurs.
     * @return bool If the execution configuration is valid, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    abstract protected function _validateExecutionConfig(&$outErrorMessage);

    /**
     * Execute an API's entry point (an SQL request or a procedure).
     * Please note that the data required for the execution of the entry point can be found within the property.
     * @param AbstractConnector $inConnector Handler to the database connector.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result|\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result
     */
    abstract protected function _execute(AbstractConnector $inConnector);

    /**
     * Initialize the entry point.
     * @param array $inConfig Entry point's configuration.
     */
    abstract protected function _init(array $inConfig=[]);

    /**
     * Return the description of the API's entry point (SQL requests or procedures).
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
        $this->_execConfig = $inExecutionConfig;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the result of the last execution.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\BaseResult Result of the last execution.
     */
    public function getResult() {
        return $this->_result;
    }

    /**
     * Get all fields' names within a given table.
     * @param string $inTableName Name of the table.
     * @param int $inOptFormat This parameter specifies the output format. Value can be:
     *        * \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint::FIELDS_RAW_AS_ARRAY: the list of fields' names is returned as an array of strings.
     *          Each element of the returned array is a string "<field name>".
     *        * \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint::FIELDS_FULLY_QUALIFIED_AS_ARRAY: the list of fully qualified fields' names is returned as an array of strings.
     *          Each element of the returned array is a string "<table name>.<field name>".
     *        * \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint::FIELDS_FULLY_QUALIFIED_AS_SQL: the list of fields' names is returned as a string "<table name>.<field name> as '<table name>.<field name>',...".
     * @param bool $inOptQuote This parameter indicates whether we should quote the fields' names or not.
     *        For example, with MySql, you can quote "user.id" into "`user`.`id`".
     * @return array The method returns the list of fields within the table.
     * @throws \Exception
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint::FIELDS_RAW_AS_ARRAY
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint::FIELDS_FULLY_QUALIFIED_AS_ARRAY
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint::FIELDS_FULLY_QUALIFIED_AS_SQL
     */
    public function getTableFieldsNames($inTableName, $inFormat=self::FIELDS_RAW_AS_ARRAY, $inOptQuote=true) {
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
        $this->_execConfig = [];
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
        if (! $this->_validateExecutionConfig($outErrorMessage)) {
            throw new \Exception("The configuration for the API's execution is not valid.\n${outErrorMessage}\nGiven configuration is: " . print_r($this->_execConfig, true));
        }

        $this->_result = $this->_execute($this->_connector);
        return $this->_result;
    }

    /**
     * Test if the request has been successfully executed.
     * @return bool If the request has been successfully executed, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    public function isSuccess() {
        return $this->_result->isSuccess();
    }

    /**
     * Test if the request failed due to an error.
     * @return bool If the request failed due to en error, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    public function isError() {
        return $this->_result->isError();
    }

    /**
     * Test if the execution of the API's entry point returned at least one data set.
     * Please note that the term "data set" represents a set of data (which forms a "row") extracted from the database.
     * A "row" of data may contain fields' values and calculated values.
     * All data sets are returned by the SGBDR to the PHP client.
     * @return bool If the execution of the API's entry point returned at least one data set, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     */
    public function isDataSetEmpty() {
        return $this->_result->isDataSetsEmpty();
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
        return $this->_execConfig;
    }
}