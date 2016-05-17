<?php

/**
 * This file presents the public static API of a schema extractor.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor;

/**
 * Interface InterfaceSchemaExtractor
 *
 * This interface defines the public static API of a schema extractor.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor
 */

interface InterfaceSchemaExtractor
{
    /**
     * Return the fully qualified name of the class the implements the connector used by this extractor.
     * @return mixed
     */
    static public function getConnectorClassName();
}