<?php

/**
 * This file defines the configuration parameters associated with SQL services.
 */

namespace dbeurive\Backend\Database\SqlService;

/**
 * Class Option
 *
 * This class defines the configuration parameters associated with SQL services.
 *
 * @package dbeurive\Backend\Database\SqlService
 */

class Option
{
    /**
     * Name of the parameter that represents the fully qualified name of the class the implements the SQL service provider for a specific database's brand.
     */
    const SQL_SERVICE_NAME = 'sql-service-class-name';
}

