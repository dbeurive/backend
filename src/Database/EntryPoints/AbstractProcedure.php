<?php

/**
 * This file implement the base class for all procedures.
 */

namespace dbeurive\Backend\Database\EntryPoints;

/**
 * Class AbstractProcedure
 *
 * Base class for all procedures.
 *
 * @package dbeurive\Backend\Database\EntryPoints
 */

abstract class AbstractProcedure extends AbstractEntryPoint
{
    /**
     * @var callable|null Function used to get a SQL request.
     */
    private $__sqlProvider = null;

    /**
     * Set the function used to get a SQL request identified by its name.
     * @param callable $inProvider The function used to get a SQL request identified by its name.
     * @throws \Exception
     */
    public function setSqlProvider(callable $inProvider) {
        if (is_null($this->__sqlProvider)) {
            $this->__sqlProvider = $inProvider;
            return;
        }
        throw new \Exception("Improper use of the method " . __METHOD__ . " detected.");
    }

    /**
     * Return a SQL request identified by its name.
     * @param string $inName Name of the SGL request.
     * @return \dbeurive\Backend\Database\EntryPoints\AbstractSql The SQL request.
     */
    protected function getSql($inName) {
        return call_user_func($this->__sqlProvider, $inName);
    }
}