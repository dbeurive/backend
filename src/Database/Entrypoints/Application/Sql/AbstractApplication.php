<?php

/**
 * This file implements the base class for all SQL requests.
 */

namespace dbeurive\Backend\Database\Entrypoints\Application\Sql;

/**
 * Class AbstractEntryPoint
 *
 * This class defines an SQL request.
 * Please note that SQL requests' organisations may be complex (with sub selections).
 * Thus, for SQL requests, the configuration's structure is free.
 *
 * @package dbeurive\Backend\Database\Entrypoints\Application\Sql
 */

abstract class AbstractApplication extends \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
{
    /**
     * @var string|array This property represents the SQL request(s).
     *      Please note that this variable may be a string or an associative array.
     *      You may set an associative array if you plan to use different SQL requests depending on the context.
     */
    private $__sql = null;

    /**
     * Set the SQL request(s).
     * @param string|array $inSql The SQL request(s) to set.
     *        Please note that this variable may be a string or an associative array.
     *        You may set an associative array if you plan to use different SQL requests depending on the context.
     * @return $this
     */
    protected function _setSql($inSql) {
        $this->__sql = $inSql;
        return $this;
    }

    /**
     * Return the SQL request(s) as a string.
     * @return string|array Return the SQL request(s).
     *         Please note that the returned value may be a string or an associative array.
     *         If the SQL request depends on the context, then the method may return an array.
     */
    protected function _getSql() {
        return $this->__sql;
    }
}