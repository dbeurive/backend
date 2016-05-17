<?php

namespace dbeurive\Backend\Database\EntryPoints;

abstract class AbstractSql extends AbstractEntryPoint
{
    /**
     * @var mixed Handler to the database.
     */
    protected $__dbh = null;
    
    /**
     * Set the handler to the database.
     * @param mixed $inDbh Handler to the database.
     * @throws \Exception
     */
    public function setDbh($inDbh) {
        if (is_null($this->__dbh)) {
            $this->__dbh = $inDbh;
            return;
        }
        throw new \Exception("Improper use of the method " . __METHOD__ . " detected.");
    }

    /**
     * Get the handler to the database.
     * @return mixed The handler to the database.
     */
    protected function getDbh() {
        return $this->__dbh;
    }
}