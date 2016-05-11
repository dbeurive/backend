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

    /**
     * {@inheritdoc}
     * @see InterfaceSqlService
     */
    static public function getFullyQualifiedFieldsAsArray($inTableName, $inFields) {
        return array_map(function($e) use ($inTableName) { return "${inTableName}.${e}"; }, $inFields);
    }

    /**
     * {@inheritdoc}
     * @see InterfaceSqlService
     */
    static public function getFullyQualifiedFieldsAsSql($inTableName, $inFields) {
        $fullyQualified = self::getFullyQualifiedFieldsAsArray($inTableName, $inFields);
        return implode(', ', array_map(function($e) { return "{$e} as '${e}'"; }, $fullyQualified));
    }

    /**
     * {@inheritdoc}
     * @see InterfaceSqlService
     */
    static public function getFullyQualifiedQuotedFieldsAsArray($inTableName, $inFields) {
        $quoter = function($inName) { return self::quoteFieldName($inName); };
        $fullyQualified = self::getFullyQualifiedFieldsAsArray($inTableName, $inFields);
        return array_map($quoter, $fullyQualified);
    }

    /**
     * {@inheritdoc}
     * @see InterfaceSqlService
     */
    static public function getFullyQualifiedQuotedFieldsAsSql($inTableName, $inFields) {
        $fullyQualified = self::getFullyQualifiedFieldsAsArray($inTableName, $inFields);
        $quoter = function($inName) { return self::quoteFieldName($inName); };
        return implode(', ', array_map(function($e) use($quoter) { return "{$quoter($e)} as '${e}'"; }, $fullyQualified));
    }
}