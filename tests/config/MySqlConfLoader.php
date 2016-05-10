<?php

/**
 * This file implements a "configuration loader" for the "documentation writer" associated to MySql.
 *
 * Usage: /Users/denisbeurive/php-public/backend/tests/config/../../src/Cli/Bin/backend db:doc-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlConfLoader
 */

namespace dbeurive\BackendTest\config;

use \dbeurive\Backend\Database\Connector\Option as ConnectorOption;

class MySqlConfLoader implements \dbeurive\Backend\Cli\InterfaceConfigLoader
{
    /**
     *  See \dbeurive\Backend\Cli\Adapter\Database\DocWriter\AbstractDocWriter for more information.
     *  The returned array must contains the following keys:
     *          * \dbeurive\Backend\Database\Doc\Option::DOC_PATH
     *          * \dbeurive\Backend\Database\Doc\Option::SCHEMA_PATH
     *          * \dbeurive\Backend\Database\Entrypoints\Option::SQL_BASE_NS
     *          * \dbeurive\Backend\Database\Entrypoints\Option::PROC_BASE_NS
     *          * \dbeurive\Backend\Database\Entrypoints\Option::SQL_REPO_PATH
     *          * \dbeurive\Backend\Database\Entrypoints\Option::PROC_REPO_PATH
     *          * \dbeurive\Backend\Cli\Option::CONFIG_LOADER_CLASS_NAME
     *          * \dbeurive\Backend\Database\Connector\Option::CONNECTOR_NAME
     *
     *  @see \dbeurive\Backend\Database\Doc\Option::DOC_PATH
     *  @see \dbeurive\Backend\Database\Doc\Option::SCHEMA_PATH
     *  @see \dbeurive\Backend\Database\Entrypoints\Option::SQL_BASE_NS
     *  @see \dbeurive\Backend\Database\Entrypoints\Option::PROC_BASE_NS
     *  @see \dbeurive\Backend\Database\Entrypoints\Option::SQL_REPO_PATH
     *  @see \dbeurive\Backend\Database\Entrypoints\Option::PROC_REPO_PATH
     *  @see \dbeurive\Backend\Cli\Option::CONFIG_LOADER_CLASS_NAME
     *  @see \dbeurive\Backend\Database\Connector\Option::CONNECTOR_NAME
     *
     */
    public function load() {
        $config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        return array_merge($config['application'], [ConnectorOption::CONNECTOR_CONFIG => $config['mysql'][ConnectorOption::CONNECTOR_CONFIG]], [ConnectorOption::CONNECTOR_NAME => $config['mysql'][ConnectorOption::CONNECTOR_NAME]]);
    }
}

