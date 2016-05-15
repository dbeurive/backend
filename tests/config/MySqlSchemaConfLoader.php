<?php

/**
 * This file implements a "configuration loader" for the "schema extractor" associated to MySql.
 *
 * Usage: /Users/denisbeurive/php-public/backend/tests/config/../../src/Cli/Bin/backend db:schema-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlSchemaConfLoader
 */

namespace dbeurive\BackendTest\config;

use \dbeurive\Backend\Database\Connector\ConfigurationParameter as ConnectorOption;
use \dbeurive\Backend\Database\Doc\ConfigurationParameter as DocOption;

class MySqlSchemaConfLoader implements \dbeurive\Backend\Cli\InterfaceConfigLoader
{
    /**
     *  See \dbeurive\Backend\Cli\Adapter\Database\DocWriter\AbstractDocWriter for more information.
     *  The returned array must contains the following keys:
     *          * \dbeurive\Backend\Database\Doc\Option::SCHEMA_PATH
     *          * MySql::DB_HOST
     *          * MySql::DB_NAME
     *          * MySql::DB_USER
     *          * MySql::DB_PASSWORD
     *          * MySql::DB_PORT
     *
     *  @see \dbeurive\Backend\Database\Doc\Option::SCHEMA_PATH
     *  @see MySql::DB_HOST
     *  @see MySql::DB_NAME
     *  @see MySql::DB_USER
     *  @see MySql::DB_PASSWORD
     *  @see MySql::DB_PORT
     */
    public function load() {
        $config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        $conf = $config['mysql'][ConnectorOption::CONNECTOR_CONFIG];
        $conf[DocOption::SCHEMA_PATH] = $config['application'][DocOption::SCHEMA_PATH];

        return $conf;
    }
}

