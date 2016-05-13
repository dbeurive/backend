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
     * Take list of fields' names and the name of a table, and return an "SQL select" fragment that contains fully qualified fields' names.
     * Example: getFullyQualifiedFieldsAsSql('user', ['id', 'name']) => "user.id as 'user.id', user.name as 'user.name'".
     * @param string $inTableName Name of the table.
     * @param array $inFields List of fields.
     * @return string The method returns an SQL fragment.
     * @throws \Exception
     */
    static public function getFullyQualifiedFieldsAsSql($inTableName, $inFields);

    /**
     * Take list of fields' names and the name of a table, and return an SQL "select" fragment that contains fully qualified, and quoted, fields' names.
     * Example: getFullyQualifiedFieldsAsSql('user', ['id', 'name']) => "`user`.`id` as 'user.id', `user`.`name` as 'user.name'".
     * @param string $inTableName Name of the table.
     * @param array $inFields List of fields.
     * @return mixed The method returns an SQL fragment.
     * @throws \Exception
     */
    static public function getFullyQualifiedQuotedFieldsAsSql($inTableName, $inFields);
}