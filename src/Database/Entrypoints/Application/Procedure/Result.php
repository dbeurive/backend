<?php

/**
 * This class implements the result of a procedure.
 */

namespace dbeurive\Backend\Database\Entrypoints\Application\Procedure;

/**
 * Class Result
 *
 * This class represents the result of a procedure.
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

class Result extends \dbeurive\Backend\Database\Entrypoints\Application\BaseResult {

    /**
     * @var array List of values calculated by the execution of the API's entry point.
     *      Please note that the term "value" represents a data that has been calculated using PHP code.
     *      A "value" is not computed by the SGBDR.
     */
    private $__values  = [];

    /**
     * Construct the result of a procedure.
     * @param int $inOptStatus Procedure's status (STATUS_NOT_EXECUTED, STATUS_SUCCESS or STATUS_ERROR).
     * @param array $inOptDataSets List of "data sets" selected by he procedure.
     *        Please note that the term "data set" represents a set of data (which forms a "row" of data).
     *        Data in a set (of data) can be:
     *            * Fields' values returned by the SGBDR.
     *            * Calculated values returned by the SGBDR.
     * @param array $inOptValues List of values computed by the procedure.
     *        Please note that the term "value" represents a data that has been calculated using PHP code.
     *        A "value" is not computed by the SGBDR.
     */
    public function __construct($inOptStatus=self::STATUS_NOT_EXECUTED, array $inOptDataSets=[], array $inOptValues=[]) {
        parent::__construct($inOptStatus, $inOptDataSets);
        $this->__values = $inOptValues;
    }

    /**
     * Set the list of values calculated by the procedure.
     * Please note that the term "value" represents a data that has been calculated using PHP code.
     * A "value" is not computed by the SGBDR.
     * @param array $inFields List of values to return.
     */
    public function setValues(array $inValues) {
        $this->__values = $inValues;
        $this->setStatusSuccess();
    }

    /**
     * Get the list of values calculated by the procedure.
     * Please note that the term "value" represents a data that has been calculated using PHP code.
     * A "value" is not computed by the SGBDR.
     * @return array The method returns the list of values calculated by the procedure.
     */
    public function getValues() {
        if ($this->getStatus() == self::STATUS_NOT_EXECUTED) {
            throw new \Exception("The API's entry point has not been executed, or you forgot to set the status of the execution !");
        }
        return $this->__values;
    }

    /**
     * Test if the execution of the procedure generated a set of values.
     * Please note that the term "value" represents a data that has been calculated using PHP code.
     * A "value" is not computed by the SGBDR.
     * @return bool If the execution of the procedure generated a set of values, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     */
    public function isValuesSetEmpty() {
        if ($this->getStatus() == self::STATUS_NOT_EXECUTED) {
            throw new \Exception("The API's entry point has not been executed, or you forgot to set the status of the execution !");
        }
        return $this->__values === [];
    }
}