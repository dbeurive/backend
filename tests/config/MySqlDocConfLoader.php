<?php

/**
 * This file implements a "configuration loader" for the "documentation writer" associated to MySql.
 *
 * Usage: /Users/denisbeurive/php-public/backend/tests/config/../../src/Cli/Bin/backend db:doc-writer --config-loader=\\dbeurive\\BackendTest\\config\\MySqlDocConfLoader
 */

namespace dbeurive\BackendTest\config;

class MySqlDocConfLoader implements \dbeurive\Backend\Cli\InterfaceConfigLoader
{
    /**
     *  See \dbeurive\Backend\Cli\Adapter\Database\DocWriter\AbstractDocWriter for more information.
     *  The returned array must contains the following keys:
     *          * \dbeurive\Backend\Database\Doc\ConfigurationParameter::DOC_PATH
     *          * \dbeurive\Backend\Database\Doc\ConfigurationParameter::SCHEMA_PATH
     *          * \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::SQL_BASE_NS
     *          * \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::PROC_BASE_NS
     *          * \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::SQL_REPO_PATH
     *          * \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::PROC_REPO_PATH
     *
     *  @see \dbeurive\Backend\Database\Doc\ConfigurationParameter::DOC_PATH
     *  @see \dbeurive\Backend\Database\Doc\ConfigurationParameter::SCHEMA_PATH
     *  @see \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::SQL_BASE_NS
     *  @see \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::PROC_BASE_NS
     *  @see \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::SQL_REPO_PATH
     *  @see \dbeurive\Backend\Database\Entrypoints\ConfigurationParameter::PROC_REPO_PATH
     */
    public function load() {
        $config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        return $config['application'];
    }
}