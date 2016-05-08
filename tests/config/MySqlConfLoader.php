<?php

/**
 * This file implements a "configuration loader" for the "documentation writer" associated to MySql.
 *
 * Usage: /Users/denisbeurive/php-public/backend/tests/config/../../src/Cli/Bin/backend db:doc-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlConfLoader
 */

namespace dbeurive\BackendTest\config;

use \dbeurive\Backend\Database\Link\Option as LinkOption;

class MySqlConfLoader implements \dbeurive\Backend\Cli\InterfaceConfigLoader
{
    /**
     *  See \dbeurive\Backend\Cli\Adapter\Database\DocWriter\AbstractDocWriter for more information.
     *  The returned array must contains the following keys:
     *          * \dbeurive\Backend\Database\Doc\Option::DOC_DB_REPO_PATH
     *          * \dbeurive\Backend\Database\Doc\Option::DOC_DB_FILE_BASENAME
     *          * \dbeurive\Backend\Database\Doc\Option::PHP_DB_DESC_PATH
     *          * \dbeurive\Backend\Database\Entrypoints\Option::SQL_BASE_NS
     *          * \dbeurive\Backend\Database\Entrypoints\Option::PROC_BASE_NS
     *          * \dbeurive\Backend\Database\Entrypoints\Option::SQL_REPO_PATH
     *          * \dbeurive\Backend\Database\Entrypoints\Option::PROC_REPO_PATH
     *          * \dbeurive\Backend\Cli\Option::CONFIG_LOADER_CLASS_NAME
     *          * \dbeurive\Backend\Database\Link::LINK_NAME
     */
    public function load() {
        $config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        return array_merge($config['application'], [LinkOption::LINK_CONFIG => $config['mysql'][LinkOption::LINK_CONFIG]], [LinkOption::LINK_NAME => $config['mysql'][LinkOption::LINK_NAME]]);
    }
}

