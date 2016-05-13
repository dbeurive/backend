<?php

/**
 * This file implements the SQL service for MySql.
 */

namespace dbeurive\Backend\Database\SqlService;
use dbeurive\Util\UtilSql;

/**
 * Class MySql
 *
 * This class implements the SQL service for MySql.
 *
 * @package dbeurive\Backend\Database\SqlService
 */

use dbeurive\Util\UtilSql\MySql as UtilMySql;

class MySql implements InterfaceSqlService
{
    /**
     * {@inheritdoc}
     * @see InterfaceSqlService
     */
    static public function getFullyQualifiedFieldsAsSql($inTableName, $inFields) {
        $fields = UtilMySql::qualifyFieldsNames($inFields, $inTableName);
        return implode(', ', array_map(function($e) { return "${e} as '${e}'"; }, $fields));
    }

    /**
     * {@inheritdoc}
     * @see InterfaceSqlService
     */
    static public function getFullyQualifiedQuotedFieldsAsSql($inTableName, $inFields) {
        $fields = UtilMySql::qualifyFieldsNames($inFields, $inTableName);
        $quoter = function($e) {
            return UtilMySql::quoteFieldName($e);
        };

        return implode(', ', array_map(function($e) use($quoter) { return "{$quoter($e)} as '${e}'"; }, $fields));
    }

}