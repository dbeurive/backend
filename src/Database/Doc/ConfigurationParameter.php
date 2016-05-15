<?php

/**
 * This file defines the name of the configuration parameters used to configure the "documentation writer".
 */

namespace dbeurive\Backend\Database\Doc;

/**
 * Class ConfigurationParameter
 *
 * This class contains the name of the configuration parameters used to configure the "documentation writer".
 *
 * @package dbeurive\Backend\Database\Doc
 */

class ConfigurationParameter {
    /**
     * This constant defines the name of the parameter used to specify the path to the file used to store the SQLite generated documentation.
     * This file contains the documentation for the database access layer.
     */
    const DOC_PATH = 'doc-path';
    /**
     * This constant defines the name of the parameter used to specify the path to the file used to store the PHP generated documentation.
     * This file contains the schema of the database.
     */
    const SCHEMA_PATH = 'schema-path';
}