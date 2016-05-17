<?php

/**
 * This file defines the configuration parameters that describes the topology of the API's entry points.
 */

namespace dbeurive\Backend\Database\EntryPoints;


/**
 * Class ConfigurationParameter
 *
 * This class contains the name of the configuration parameters used to describe the API's entry's points.
 *
 * @package dbeurive\Backend\Database\EntryPoints
 */

class ConfigurationParameter {

    /**
     * This constant defines the name of the configuration parameter that represents the base name space for all SQL requests.
     */
    const SQL_BASE_NS = 'sql-base-namespace';
    /**
     * This constant defines the name of the configuration parameter that represents the path to the base repository for all SQL requests.
     */
    const SQL_REPO_PATH = 'sql-repository-path';
    /**
     * This constant defines the name of the configuration parameter that represents the base name space for all procedures.
     */
    const PROC_BASE_NS = 'procedure-base-namespace';
    /**
     * This constant defines the name of the configuration parameter that represents the path to the base repository for all procedures.
     */
    const PROC_REPO_PATH = 'procedure-repository-path';
    /**
     * This constant defines the name of the configuration parameter that represents an instance of a database handler (an instance of \PDO, for example).
     */
    const DB_HANDLER = 'db-handler';
}