<?php

/**
 * This file contains the configurations' options for the database connector.
 */

namespace dbeurive\Backend\Database\Connector;

/**
 * Class Option
 *
 * This class defines the configurations' options for the database connector.
 *
 * @package dbeurive\Backend\Database\Connector
 */

class Option
{
    /**
     * This constant represents the name of the configuration parameter that defines the fully qualified name of the class that implements the "database connector".
     */
    const CONNECTOR_NAME = 'db-connector-class-name';
    /**
     * This constant represents the name of the configuration parameter that defines the configuration for the "database connector".
     */
    const CONNECTOR_CONFIG = 'db-connector-config';
}