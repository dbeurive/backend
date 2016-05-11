<?php

/**
 * This file defines the interface of an "SQL service".
 *
 * SQL services provide functionalities that are specific to a given brand of database server, but that is not provided by the low level database handler.
 * Right now, there is only one such functionality: quoting fully qualified fields' names.
 * For example (MySql): user.id => `user`.`id`.
 * Please note that, because these functionalities does not require an open connexion to the database, they are implemented as static methods.
 * These functionalities can be used without configuring the connector first.
 */

namespace dbeurive\Backend\Database\SqlService;

/**
 * Interface InterfaceSqlService
 *
 * This interface describes an "SQL service".
 *
 * @package dbeurive\Backend\Database\SqlService
 */

interface InterfaceSqlService
{
    /**
     * Quote a field's name.
     * Example: \dbeurive\Backend\Database\SqlService\MySql::quoteFieldName
     * @param string $inFieldName Name of the field to quote.
     * @return string The method returns the quoted field.
     *
     * @see \dbeurive\Backend\Database\SqlService\MySql::quoteFieldName
     */
    static public function quoteFieldName($inFieldName);

    /**
     * Take an array of fields' names and return a array of fully qualified fields' names.
     * Example: getFullyQualifiedFieldsAsArray('user', ['id', 'name']) => ['user.id', 'user.name']
     * @param string $inTableName Name of the table.
     * @param array $inFields List of fields.
     * @return array The method returns an array of fully qualified fields' names.
     */
    static public function getFullyQualifiedFieldsAsArray($inTableName, $inFields);

    /**
     * Take an array of fields' names and return an SQL fragment that contains fully qualified fields' names.
     * Exemple: getFullyQualifiedFieldsAsSql('user', ['id', 'name']) => "user.id as 'user.id', user.name as 'user.name'".
     * @param string $inTableName Name of the table.
     * @param array $inFields List of fields.
     * @return string The method returns an SQL fragment.
     */
    static public function getFullyQualifiedFieldsAsSql($inTableName, $inFields);

    static public function getFullyQualifiedQuotedFieldsAsArray($inTableName, $inFields);

    static public function getFullyQualifiedQuotedFieldsAsSql($inTableName, $inFields);
}