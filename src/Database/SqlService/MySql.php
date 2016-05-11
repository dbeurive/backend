<?php

/**
 * This file implements the SQL service for MySql.
 */

namespace dbeurive\Backend\Database\SqlService;

/**
 * Class MySql
 *
 * This class implements the SQL service for MySql.
 *
 * @package dbeurive\Backend\Database\SqlService
 */

class MySql implements InterfaceSqlService
{
    /**
     * {@inheritdoc}
     * @see InterfaceSqlService
     */
    static public function quoteFieldName($inFieldName) {
        $tokens = explode('.', $inFieldName);
        if (2 != count($tokens)) {
            throw new \Exception("Invalid field's name ${inFieldName}");
        }
        return '`' . $tokens[0] . '`' . '.' . '`' . $tokens[1] . '`';
    }
}