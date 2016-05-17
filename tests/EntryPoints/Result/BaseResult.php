<?php

/**
 * This file implements the base class for all API's entry points results.
 */

namespace dbeurive\BackendTest\EntryPoints\Result;

/**
 * Class BaseResult
 *
 * This class is the base class for all API's entry points' results.
 *
 * Please note that all API's entry points may return "data sets".
 * The term "data set" represents a set of values (which forms a "row" of values).
 * Values in a set (of values) can be:
 *   * Fields' values returned by the SGBDR.
 *   * Calculated values returned by the SGBDR.
 *
 * Please note that data sets may be returned by SQL requests or procedures.
 *
 * @package dbeurive\Backend\Database\EntryPoints
 */

class BaseResult {

    /**
     * This constant represents the execution' status "not executed".
     */
    const STATUS_NOT_EXECUTED = 0;
    /**
     * This constant represents the execution' status "success".
     */
    const STATUS_SUCCESS = 1;
    /**
     * This constant represents the execution' status "failure".
     */
    const STATUS_ERROR = 2;

    /**
     * @var array The data sets returned by the API's entry point.
     * The "data sets" returned by the API's entry point.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *   * Fields' values returned by the SGBDR.
     *   * Calculated values returned by the SGBDR.
     */
    private $__dataSets  = [];
    /**
     * @var int Status of the API's entry point's execution.
     */
    private $__status  = self::STATUS_NOT_EXECUTED;
    /**
     * @var null|string Error message.
     */
    private $__message = null;

    /**
     * Construct a result.
     * @param int $inOptStatus Status if the result. This value can be:
     *        * \dbeurive\BackendTest\EntryPoints\Result\BaseResult::STATUS_NOT_EXECUTED
     *        * \dbeurive\BackendTest\EntryPoints\Result\BaseResult::STATUS_SUCCESS
     *        * \dbeurive\BackendTest\EntryPoints\Result\BaseResult::STATUS_ERROR
     * @param array $inOptDataSets List of data sets selected by the execution of the API's entry point.
     * 
     * @see \dbeurive\BackendTest\EntryPoints\Result\BaseResult::STATUS_NOT_EXECUTED
     * @see \dbeurive\BackendTest\EntryPoints\Result\BaseResult::STATUS_SUCCESS
     * @see \dbeurive\BackendTest\EntryPoints\Result\BaseResult::STATUS_ERROR
     */
    public function __construct($inOptStatus=self::STATUS_NOT_EXECUTED, $inOptDataSets=[]) {
        $this->__status = $inOptStatus;
        $this->__dataSets = $inOptDataSets;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Status' management.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the result's status to "success".
     */
    public function setStatusSuccess() {
        $this->__status = self::STATUS_SUCCESS;
    }

    /**
     * Set the result's status to "error".
     */
    public function setStatusError() {
        $this->__status = self::STATUS_ERROR;
    }

    /**
     * Return the status of the operation.
     * @return int The function returns the status of the operation.
     */
    public function getStatus() {
        return $this->__status;
    }

    /**
     * Test if an error occurred while executing the API's entry point.
     * @return bool If an error occurred, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     */
    public function isError() {
        if ($this->__status == self::STATUS_NOT_EXECUTED) {
            throw new \Exception("The API's entry point has not been executed, or you forgot to set the status of the execution !");
        }
        return $this->__status == self::STATUS_ERROR;
    }

    /**
     * Test if the execution of the API's entry point was successful.
     * @return bool If the API's entry point was successful, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     */
    public function isSuccess() {
        if ($this->__status == self::STATUS_NOT_EXECUTED) {
            throw new \Exception("The API's entry point has not been executed, or you forgot to set the status of the execution !");
        }
        return $this->__status == self::STATUS_SUCCESS;
    }
    
    // -----------------------------------------------------------------------------------------------------------------
    // Data set management.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the "data sets" returned by the API's entry point.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *   * Fields' values returned by the SGBDR.
     *   * Calculated values returned by the SGBDR.
     * @param array $inDataSets Data sets.
     */
    public function setDataSets(array $inDataSets) {
        $this->__dataSets = $inDataSets;
        $this->__status = self::STATUS_SUCCESS;
    }

    /**
     * Get the "data sets" returned by the API's entry point.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *   * Fields' values returned by the SGBDR.
     *   * Calculated values returned by the SGBDR.
     * @return array The method returns an array that contains the data sets.
     * @throws \Exception
     */
    public function getDataSets() {
        if ($this->__status == self::STATUS_NOT_EXECUTED) {
            throw new \Exception("The API's entry point has not been executed, or you forgot to set the status of the execution !");
        }
        return $this->__dataSets;
    }

    /**
     * Test if the execution of the API's entry point returned at least one "data set".
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *   * Fields' values returned by the SGBDR.
     *   * Calculated values returned by the SGBDR.
     * @return bool If the execution of the API's entry point returned at least one data set, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     * @throws \Exception
     */
    public function isDataSetsEmpty() {
        if ($this->__status == self::STATUS_NOT_EXECUTED) {
            throw new \Exception("The API's entry point has not been executed, or you forgot to set the status of the execution !");
        }
        return $this->__dataSets === [];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Message's management.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set an error message.
     * @param string $inErrorMessage The error message.
     */
    public function setErrorMessage($inErrorMessage) {
        $this->__message = $inErrorMessage;
        $this->__status = self::STATUS_ERROR;
    }

    /**
     * Get the error message.
     * @return string|null if the  execution of the API's entry point was not successful, then the method returns an error message.
     *         Otherwise, the method returns the value null.
     */
    public function getErrorMessage() {
        if ($this->__status == self::STATUS_NOT_EXECUTED) {
            throw new \Exception("The API's entry point has not been executed, or you forgot to set the status of the execution !");
        }
        return $this->__message;
    }

}