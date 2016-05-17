<?php

/**
 * This file implements a set of new assertions for PHPUnit.
 */



namespace dbeurive\BackendTest\EntryPoints\UnitTests\PhpUnit;

use dbeurive\BackendTest\EntryPoints\Result\ProcedureResult as ProcedureResult;
use dbeurive\BackendTest\EntryPoints\Result\SqlResult as RequestResult;

/**
 * Class PHPUnit_Backend_TestCase
 *
 * This class implements a set of new assertions for PHPUnit.
 *
 * @package dbeurive\BackendTest\EntryPoints\UnitTests\PhpUnit
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
     * @param ProcedureResult $inProcedureResult The procedure's result.
     */
    public function assertResultValueSetIsEmpty(ProcedureResult $inProcedureResult) {
        $this->assertCount(0, $inProcedureResult->getValues());
    }

    /**
     * Assert that the procedure returned "output values".
     * Please note that the term "output value" represents a data that has been calculated using PHP code.
     * An "output value" is not computed by the SGBDR.
     * @param ProcedureResult $inProcedureResult The procedure's result.
     */
    public function assertResultValueSetIsNotEmpty(ProcedureResult $inProcedureResult) {
        $this->assertFalse(count($inProcedureResult->getValues()) == 0);
    }

    /**
     * Assert that the procedure returned an expected number of "output values".
     * Please note that the term "output value" represents a data that has been calculated using PHP code.
     * An "output value" is not computed by the SGBDR.
     * @param int $inCount Expected number of "output values".
     * @param ProcedureResult $inProcedureResult Procedure's result.
     */
    public function assertResultValuesCount($inCount, ProcedureResult $inProcedureResult) {
        $this->assertCount($inCount, $inProcedureResult->getValues());
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Entry points
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Assert that the number of returned "data sets" is equal to a given count.
     * @param int $inCount Expected number of "data sets".
     * @param ProcedureResult|RequestResult $inProcedureOrRequestResult The result of a SQL request or a procedure.
     */
    public function assertResultDataSetCount($inCount, $inProcedureOrRequestResult) {
        $this->assertCount($inCount, $inProcedureOrRequestResult->getDataSets());
    }

    /**
     * Assert that the an API's entry point returned no row.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *    * Fields' values returned by the SGBDR.
     *    * Calculated values returned by the SGBDR.
     * @param ProcedureResult|RequestResult $inProcedureOrRequestResult The result of a SQL request or a procedure.
     */
    public function assertResultDataSetIsEmpty($inProcedureOrRequestResult) {
        $this->assertTrue($inProcedureOrRequestResult->isDataSetsEmpty());
    }

    /**
     * Assert that an API's entry point returned at least one row.
     * The term "data set" represents a set of data (which forms a "row" of data).
     * Data in a set (of data) can be:
     *    * Fields' values returned by the SGBDR.
     *    * Calculated values returned by the SGBDR.
     * @param ProcedureResult|RequestResult $inProcedureOrRequestResult The result of a SQL request or a procedure.
     */
    public function assertResultDataSetIsNotEmpty($inProcedureOrRequestResult) {
        $this->assertFalse($inProcedureOrRequestResult->isDataSetsEmpty());
    }

    /**
     * Assert that the status of an API's entry point's execution is OK.
     * @param ProcedureResult|RequestResult $inProcedureOrRequestResult The procedure or its result.
     */
    public function assertStatusIsOk($inProcedureOrRequestResult) {
        $this->assertTrue($inProcedureOrRequestResult->isSuccess());
    }

    /**
     * Assert that the status of an API's entry point's execution is not OK.
     * @param ProcedureResult|RequestResult $inProcedureOrRequestResult The result of a SQL request or a procedure.
     */
    public function assertStatusIsNotOk($inProcedureOrRequestResult) {
        $this->assertTrue($inProcedureOrRequestResult->isError());
    }

}