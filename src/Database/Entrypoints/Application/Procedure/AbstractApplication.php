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
 *
 * Please note that a procedure's configuration should not be complex.
 * However, in practice it could be (most likely due to a bad design): some parameters or fields may be mandatory depending on a context of execution.
 * This is the reason why it is possible to specify an arbitrary structure as procedures' configuration (see setFreeExecutionConfig())
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
    // -----------------------------------------------------------------------------------------------------------------
    // Protected methods that should be used within the procedures.
    // -----------------------------------------------------------------------------------------------------------------

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
}
