<?php

use dbeurive\Backend\Database\Entrypoints\Option as EntryPointOption;
use dbeurive\Backend\Database\Doc\Option as DocOption;
use dbeurive\Backend\Database\Link\Option as LinkOption;
use dbeurive\Backend\Database\Link\MySql;


return call_user_func(function() {

    // -----------------------------------------------------------------------------------------------------------------
    // Set environmental constants.
    // -----------------------------------------------------------------------------------------------------------------

    $TEST_BASE_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..';

    // -----------------------------------------------------------------------------------------------------------------
    // Generic configuration for the connexion to the MySql server.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This array contains the configuration required to open a connection to the MySql database.
     * Please, customise the values.
     */
    $mysqlConf = [
        MySql::DB_HOST      => 'localhost',
        MySql::DB_NAME      => 'phptools',
        MySql::DB_USER      => 'root',
        MySql::DB_PASSWORD  => '',
        MySql::DB_PORT      => 3306
    ];

    // -----------------------------------------------------------------------------------------------------------------
    // Configuration for the backend interface.
    // -----------------------------------------------------------------------------------------------------------------

    $dir = [ 'EntryPoints', 'Brands', 'MySql' ];
    $baseEntryPointDir = $TEST_BASE_DIR . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $dir);

    /**
     * This value contains the configuration from the backend's configuration.
     * Please note that the current package is intended to be used to test all supported database servers.
     * That's why we defined the entry "mysql". In the future, new entries will be added as new databases will be supported.
     * Keep in mind that the entry "mysql" is here only for the sake of testing the package.
     *
     * sql-repository-path:       Path to the directory that contains all the SQL requests
     * procedure-repository-path: Path to the directory that contains all the procedures
     * sql-base-namespace:        Base namespace for all the SQL requests
     * procedure-base-namespace:  Base namespace for all the procedures
     * doc-db-repository-path:    Path to the directory used to store the generated documentation
     * doc-db-basename:           Base name for the generated files used to store the documentation
     * php-db-desc-path:          Path to the generated PHP file used to store the list of tables and fields within the database
     * db-link-class-name:        Name of the class that implements the database link
     * db-link-config:            Configuration for the database link
     */
    $conf = [
        'test' => [
            'dir.cache'      => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'cache',
            'dir.fixtures'   => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'fixtures',
            'dir.references' => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'references',
        ],
        'application' => [
            EntryPointOption::SQL_REPO_PATH     => $baseEntryPointDir . DIRECTORY_SEPARATOR . 'Sqls',
            EntryPointOption::PROC_REPO_PATH    => $baseEntryPointDir . DIRECTORY_SEPARATOR . 'Procedures',
            EntryPointOption::SQL_BASE_NS       => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls',
            EntryPointOption::PROC_BASE_NS      => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures',
            DocOption::DOC_DB_REPO_PATH         => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'cache',
            DocOption::DOC_DB_FILE_BASENAME     => 'mysql_db_schema',
            DocOption::PHP_DB_DESC_PATH         => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'mysql_db_schema.php'
        ],
        'mysql' => [
            LinkOption::LINK_NAME               => '\\dbeurive\\Backend\\Database\\Link\\MySql',
            LinkOption::LINK_CONFIG             => $mysqlConf
        ]
    ];

    // -----------------------------------------------------------------------------------------------------------------
    // Return the backend's configuration.
    // -----------------------------------------------------------------------------------------------------------------

    return $conf;
});

