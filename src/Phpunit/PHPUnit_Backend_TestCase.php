<?php

/**
 * This file implements a set of new assertions for PHPUnit.
 */

namespace dbeurive\Backend\Phpunit;

use \dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication as SqlRequest;
use \dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication as Procedure;
use dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result as ProcedureResult;

/**
 * Class PHPUnit_Backend_TestCase
 *
 * This class implements a set of new assertions for PHPUnit.
 *
 * @package dbeurive\Backend\Phpunit
 */

class PHPUnit_Backend_TestCase extends \PHPUnit_Framework_TestCase
{

    // -----------------------------------------------------------------------------------------------------------------
    // SQL requests
    // -----------------------------------------------------------------------------------------------------------------

    // ... no specific method.

    // -----------------------------------------------------------------------------------------------------------------
    // Procedures
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Assert that the procedure returned no value.
     * Please note that the term "output value" represents a data that has been calculated using PHP code.
     * An "output value" is not computed by the SGBDR.
     * @param Procedure|ProcedureResult $inProcedureOrResult The procedure or its result.
     */
    public function assertResultValueSetIsEmpty($inProcedureOrResult) {
        $this->assertCount(0, $this->__getValuesFromProcedure($inProcedureOrResult));
    }

    /**
     * Assert that the procedure returned "output values".
     * Please note that the term "output value" represents a data that has been calculated using PHP code.
     * An "output value" is not computed by the SGBDR.
     * @param Procedure|ProcedureResult $inProcedureOrResult The procedure or its result.
     */
    public function assertResultValueSetIsNotEmpty($inProcedureOrResult) {
        $this->assertFalse(count($this->__getValuesFromProcedure($inProcedureOrResult)) == 0);
    }

    /**
     * Assert that the procedure returned an expected number of "output values".
     * Please note that the term "output value" represents a data that has been calculated using PHP code.
     * An "output value" is not computed by the SGBDR.
     * @param int $inCount Expected number of "output values".
     * @param ProcedureResult|Procedure $inProcedureOrResult Procedure or its result.
     */
    public function assertResultValuesCount($inCount, $inProcedureOrResult) {
        $this->assertCount($inCount, $this->__getValuesFromProcedure($inProcedureOrResult));
    }

    /**
     * Return the set of values returned by a given procedure, or a given procedure's result.
     * @param ProcedureResult|Procedure $inProcedureOrResult Procedure or its result.
     * @return array The method returns the set of values.
     */
    private function __getValuesFromProcedure($inProcedureOrResult) {
        $values = [];

        if ($inProcedureOrResult instanceof Procedure) {
            /** @var Procedure $inProcedureOrResult */
            /** @var ProcedureResult $result */
            $result = $inProcedureOrResult->getResult();
            $values = $result->getValues();
        } else {
            /** @var ProcedureResult $inProcedureOrResult */
            $values = $inProcedureOrResult->getValues();
        }

        return $values;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Entry points
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Assert that an API's entry point has been executed.
     * @param Procedure|SqlRequest $inProcedureOrRequest Procedure or SQL request.
     */
    public function assertHasBeenExecuted($inProcedureOrRequest) {
        $this->assertTrue($inProcedureOrRequest->hasBeenExecuted());
    }

    /**
     * Assert that an API's entry point has not been executed.
     * @param Procedure|SqlRequest $inProcedureOrRequest Procedure or SQL request.
     */
    public function assertHasNotBeenExecuted($inProcedureOrRequest) {
        $this->assertFalse($inProcedureOrRequest->hasBeenExecuted());
    }

    /**
     * Assert that the number of returned "data sets" is equal to a given count.
     * @param int $inCount Expected number of "data sets".
     * @param Procedure|SqlRequest $inProcedureOrRequest The SQL request or its result.
     */
    public function assertResultDataSetCount($inCount, $inProcedureOrRequest) {
        $this->assertCount($inCount, $inProcedureOrRequest->getResult()->getDataSets());
    }

    /**
     * Assert that the an API's entry point returned no row.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *    * Fields' values returned by the SGBDR.
     *    * Calculated values returned by the SGBDR.
     * @param Procedure|SqlRequest $inProcedureOrRequest Procedure or SQL request.
     */
    public function assertResultDataSetIsEmpty($inProcedureOrRequest) {
        $this->assertTrue($inProcedureOrRequest->isDataSetEmpty());
    }

    /**
     * Assert that an API's entry point returned at least one row.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *    * Fields' values returned by the SGBDR.
     *    * Calculated values returned by the SGBDR.
     * @param Procedure|SqlRequest $inProcedureOrRequest Procedure or SQL request.
     */
    public function assertResultDataSetIsNotEmpty($inProcedureOrRequest) {
        $this->assertFalse($inProcedureOrRequest->getResult()->isDataSetsEmpty());
    }

    /**
     * Assert that the status of an API's entry point's execution is OK.
     * @param Procedure|SqlRequest $inProcedureOrRequest The procedure or its result.
     */
    public function assertStatusIsOk($inProcedureOrRequest) {
        $this->assertTrue($inProcedureOrRequest->isSuccess());
    }

    /**
     * Assert that the status of an API's entry point's execution is not OK.
     * @param Procedure|SqlRequest $inProcedureOrRequest The procedure or its result.
     */
    public function assertStatusIsNotOk($inProcedureOrRequest) {
        $this->assertTrue($inProcedureOrRequest->isError());
    }

}