<?php

/**
 * This file contains the configurations' options for the database link.
 */

namespace dbeurive\Backend\Database\Link;

/**
 * Class Option
 *
 * This class defines the configurations' options for the database link.
 *
 * @package dbeurive\Backend\Database\Link
 */

class Option
{
    /**
     * This constant represents the name of configuration parameter that defines the fully qualified name of the class that implements the "database link".
     */
    const LINK_NAME = 'db-link-class-name';
    /**
     * This constant represents the name of configuration parameter that defines the configuration for the "database link".
     */
    const LINK_CONFIG = 'db-link-config';
}