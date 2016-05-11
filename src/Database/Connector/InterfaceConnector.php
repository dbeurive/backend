<?php

/**
 * This file defines the static interface of a "connector".
 *
 * Connectors' static methods implement functionalities that don't rely on an open connexion to the database server.
 * These functionalities are not provided by low-level database handlers such as PDO.
 */

namespace dbeurive\Backend\Database\Connector;

/**
 * Interface InterfaceConnector
 *
 * This interface defines the static interface of a "connector".
 *
 * @package dbeurive\Backend\Database\Connector
 */

interface InterfaceConnector
{
    /**
     * Quote a field's name.
     * Example: \dbeurive\Backend\Database\Connector\MySqlPdo::quoteFieldName
     * @param string $inFieldName Name of the field to quote.
     * @return string The method returns the quoted field.
     *
     * @see \dbeurive\Backend\Database\Connector\MySqlPdo::quoteFieldName
     */
    static public function quoteFieldName($inFieldName);
}