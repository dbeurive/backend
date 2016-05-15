<?php

/**
 * This file implements the base class for all procedures.
 */

namespace dbeurive\Backend\Database\Entrypoints\Application\Procedure;
use dbeurive\Util\UtilArray;
use dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription;

/**
 * Class AbstractEntryPoint
 *
 * This class represents the base class for all procedures.
 * Please note that a procedure's configuration should not be complex.
 * However, in practice it could be (most likely due to a bad design): some parameters or fields may be mandatory depending on a context of execution.
 * This is the reason why it is possible to specify an arbitrary structure as procedures' configuration (see setExecutionConfig())
 *
 * The execution's configuration is an array that presents two keys:
 *    * AbstractEntryPoint::CONF_IN_FIELDS: the list of (database) fields used by the procedure.
 *    * AbstractEntryPoint::CONF_IN_PARAMS: the list of parameters for the procedure.
 *
 * NOTES:
 *
 *     The term "data set" represents a set of data (which forms a "row" of data).
 *     Data in a set (of data) can be:
 *         * Fields' values returned by the SGBDR.
 *         * Calculated values returned by the SGBDR.
 *
 *     The term "value" represents a data that has been calculated using the PHP code.
 *     A "value" is not computed by the SGBDR.
 *
 * @package dbeurive\Backend\Database\Entrypoints\Application\Procedure
 */

abstract class AbstractApplication extends \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
{
    const CONF_IN_FIELDS = 'inField';
    const CONF_IN_PARAMS = 'inParams';

    // -----------------------------------------------------------------------------------------------------------------
    // Public methods that should be used within the "controllers".
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Add an input field to the procedure's configuration.
     *
     * @param string $inFieldName Name of the field.
     * @param string $inFieldValue Value of the field.
     * @return $this
     */
    public function addInputField($inFieldName, $inFieldValue) {
        if (! array_key_exists(self::CONF_IN_FIELDS, $this->_executionConfig)) {
            $this->_executionConfig[self::CONF_IN_FIELDS] = [];
        }
        $this->_executionConfig[self::CONF_IN_FIELDS][$inFieldName] = $inFieldValue;
        return $this;
    }

    /**
     * Set the list of input fields for the procedure's configuration.
     *
     * @param array $inFields List of input fields.
     *        An input field is an array of two elements.
     *        * The first element is the name of the field.
     *        * The second element is the value of the field.
     * @return $this
     */
    public function setInputFields(array $inFields) {
        foreach ($inFields as $_field) {
            $name = $_field[0];
            $value = $_field[1];
            $this->addInputField($name, $value);
        }
        return $this;
    }

    /**
     * Add an input parameter to the procedure's configuration.
     *
     * @param string $inFieldName Name of the parameter.
     * @param string $inFieldValue Value of the parameter.
     * return $this
     */
    public function addInputParam($inParamName, $inParamValue) {
        if (! array_key_exists(self::CONF_IN_PARAMS, $this->_executionConfig)) {
            $this->_executionConfig[self::CONF_IN_PARAMS] = [];
        }
        $this->_executionConfig[self::CONF_IN_PARAMS][$inParamName] = $inParamValue;
        return $this;
    }

    /**
     * Set the list of input parameters for the procedure's configuration.
     *
     * @param array $inParams List of input parameters.
     *        An input parameter is an array of two elements.
     *        * The first element is the name of the parameter.
     *        * The second element is the value of the parameter.
     * @return $this
     */
    public function setInputParams(array $inParams) {
        foreach ($inParams as $_param) {
            $name = $_param[0];
            $value = $_param[1];
            $this->addInputParam($name, $value);
        }
        return $this;
    }

    /**
     * Set the procedure's configuration.
     *
     * Please note that a procedure's configuration should not be complex.
     * However, in practice it could be (most likely due to a bad design) : some parameters or fields may be mandatory depending on a context of execution.
     * Therefore, the API does not require to declare fields or parameters as mandatory.
     *
     * Furthermore, you may need to set up an extremely complex configuration that cannot be expressed via a set of input fields and parameters.
     * To do that, you can use the method setExecutionConfig().
     * 
     * @param array $inExecutionConfig Configuration to set.
     * @return $this
     */
    public function setExecutionConfig(array $inExecutionConfig) {
        return $this->_setExecutionConfig($inExecutionConfig);
    }

    /**
     * Test whether the result of the procedure's execution returned an empty set of values.
     * Please note that the term "value" represents a data that has been calculated using PHP code.
     * A "value" is not computed by the SGBDR.
     * @return bool If the procedure's execution returned an empty set of values, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    public function isValuesSetEmpty() {
        return $this->_result->isValuesSetEmpty();
    }

    /**
     * Test whether the result of the procedure's execution returned at least one "data set" or not.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *   * Fields' values returned by the SGBDR.
     *   * Calculated values returned by the SGBDR.
     * @return bool If the execution of the API's entry point returned at least one data set, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     * @throws \Exception
     */
    public function isDataSetEmpty() {
        return $this->_result->isDataSetsEmpty();
    }

    /**
     * Return the values calculated by the procedure.
     * Please note that the term "value" represents a data that has been calculated using PHP code.
     * A "value" is not computed by the SGBDR.
     * @return array The method returns the values calculated by the procedure.
     * @throws \Exception
     */
    public function getValues() {
        $result = $this->__getResult();
        return $result->getValues();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Protected methods that should be used within the procedures.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the list of input fields in the procedure's configuration.
     * @param array|null $inOptExecutionConfig Execution configuration.
     *        If this parameter is not set, then the method uses the value that has been set previously.
     * @return array
     */
    protected function _getInputFields(array $inOptExecutionConfig=null) {

        $conf = is_null($inOptExecutionConfig) ? $this->_executionConfig : $inOptExecutionConfig;

        if (! array_key_exists(self::CONF_IN_FIELDS, $conf)) {
            return [];
        }
        return $conf[self::CONF_IN_FIELDS];
    }

    /**
     * Return the list of input parameters in the procedure's configuration.
     * @param array|null $inOptExecutionConfig Execution configuration.
     *        If this parameter is not set, then the method uses the value that has been set previously.
     * @return array
     */
    protected function _getInputParams(array $inOptExecutionConfig=null) {

        $conf = is_null($inOptExecutionConfig) ? $this->_executionConfig : $inOptExecutionConfig;

        if (! array_key_exists(self::CONF_IN_PARAMS, $conf)) {
            return [];
        }
        return $conf[self::CONF_IN_PARAMS];
    }

    /**
     * Return an SQL request identified by its name.
     * @param string $inName Name of the SQL request.
     * @param array $inInitConfig Configuration for the SQL request's construction.
     *        This parameter is mandatory (but it could be an empty array, if the SQL request's construction does not require any specific configuration).
     * @param array $inExecutionConfig Configuration for the SQL request execution.
     *        Typically, this array contains the values of the fields required by the request's execution.
     *        If this parameter is specified, then the given configuration is assigned to the returned object.
     *        Otherwise, no execution configuration is applied to the returned object.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication
     */
    protected function _getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null) {
        return $this->_provider->getSql($inName, $inInitConfig, $inExecutionConfig);
    }

    /**
     * Check that the execution's configuration contains all the mandatory input fields.
     * Please note that a procedure's configuration should not be complex.
     * However, in practice it could be (most likely due to a bad design) : some fields may be mandatory depending on a context of execution.
     * Therefore, this method is not automatically called before the procedure's execution.
     * This method is provided because it is a convenient way to check the execution's configuration against the procedure's description, if the procedure's configuration does not depend on the context.
     * @param array|null $inOptExecutionConfig Execution's configuration.
     *        If this parameter is not set, then the method uses the value that may have been set previously.
     * @return bool If the execution's configuration contains all the mandatory input fields, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    protected function _checkMandatoryInputFields(array $inOptExecutionConfig=null) {

        $inputFields = $this->_getInputFields($inOptExecutionConfig);

        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Procedure $description */
        $description = $this->getDescription();
        $mandatoryInputFields = $this->__keepNames($description->getMandatoryInputFields_());
        return UtilArray::array_keys_exists($mandatoryInputFields, $inputFields);
    }

    /**
     * Check that the execution's configuration contains all the mandatory input parameters.
     * Please note that a procedure's configuration should not be complex.
     * However, in practice it could be (most likely due to a bad design) : some parameters may be mandatory depending on a context of execution.
     * Therefore, this method is not automatically called before the procedure's execution.
     * This method is provided because it is a convenient way to check the execution's configuration against the procedure's description, if the procedure's configuration does not depend on the context.
     * @param array|null $inOptExecutionConfig Execution configuration.
     *        If this parameter is not set, then the method uses the value that may have been set previously.
     * @return bool If the execution's configuration contains all the mandatory input parameters, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    protected function _checkMandatoryInputParams(array $inOptExecutionConfig=null) {

        $inputParams = $this->_getInputParams($inOptExecutionConfig);

        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Procedure $description */
        $description = $this->getDescription();
        $mandatoryInputParams = $this->__keepNames($description->getMandatoryInputParams_());
        return UtilArray::array_keys_exists($mandatoryInputParams, $inputParams);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Private methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This method takes as input the list of mandatory fields or the list of mandatory parameters, as returned by one of the following method:
     *   *  "\dbeurive\Backend\Database\Entrypoints\Description\Procedure::getMandatoryInputFields_"
     *   *  "\dbeurive\Backend\Database\Entrypoints\Description\Procedure::getMandatoryInputParams_"
     * and it returns the "names" (which may be the fields' names or the parameters' names).
     *
     * @param array $inMandatoryFieldsOrParams This value should be the result of the call to one of the following methods:
     *   *  "\dbeurive\Backend\Database\Entrypoints\Description\Procedure::getMandatoryInputFields_"
     *   *  "\dbeurive\Backend\Database\Entrypoints\Description\Procedure::getMandatoryInputParams_"
     * 
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Procedure::getMandatoryInputFields_
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Procedure::getMandatoryInputParams_
     */
    private function __keepNames(array $inMandatoryFieldsOrParams) {
        return array_map(function($e) { return $e[AbstractDescription::KEY_NAME]; }, $inMandatoryFieldsOrParams);
    }

    /**
     * The Returns the procedure's result.
     * @return Result The procedure's result.
     */
    private function __getResult() {
        return $this->_result;
    }

}
