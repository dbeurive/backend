<?php

use dbeurive\Backend\Database\EntryPoints\ConfigurationParameter as EntryPointOption;
use dbeurive\Backend\Database\Doc\ConfigurationParameter as DocOption;
use dbeurive\Backend\Cli\Adapter\Database\Connector\ConfigurationParameter as ConnectorParameter;
use dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo;


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
     *
     * !!! WARNING !!!
     *
     * You can change the name of the database. However, if you do so, then you must also modify the script that creates the database.
     * See the file "tests/fixtures/MySql/schema.php".
     */
    $mysqlConf = [
        MySqlPdo::DB_HOST      => 'localhost',
        MySqlPdo::DB_NAME      => 'phptools',  // !!! WARNING !!! See the file "tests/fixtures/MySql/schema.php"
        MySqlPdo::DB_USER      => 'root',
        MySqlPdo::DB_PASSWORD  => '',
        MySqlPdo::DB_PORT      => 3306
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
     * doc-path:                  Path to the SQLite database that will be generated.
     * schema-path:               Path to the generated PHP file used to store the list of tables and fields within the database
     * sql-service-class-name:    Fully qualified name of the class that provides the SQL services for the database.
     * db-connector-config:       Configuration for the database connector
     */
    $conf = [
        'test' => [
            'dir.cache'      => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'cache',
            'dir.fixtures'   => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'fixtures',
            'dir.references' => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'references',
        ],
        'application' => [
            EntryPointOption::SQL_REPO_PATH  => $baseEntryPointDir . DIRECTORY_SEPARATOR . 'Sqls',
            EntryPointOption::PROC_REPO_PATH => $baseEntryPointDir . DIRECTORY_SEPARATOR . 'Procedures',
            EntryPointOption::SQL_BASE_NS    => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls',
            EntryPointOption::PROC_BASE_NS   => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures',
            DocOption::DOC_PATH              => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'mysql_doc.sqlite',
            DocOption::SCHEMA_PATH           => $TEST_BASE_DIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'mysql_schema.php'
        ],
        'mysql' => $mysqlConf,
        'mysql-connector' => [
            // This parameter is used by the schema extractor.
            ConnectorParameter::CONNECTOR_CONFIG => $mysqlConf,
            // This represents the name of the class that implements the database connector.
            ConnectorParameter::CONNECTOR_CLASS_NAME => '\\dbeurive\\Backend\\Database\\Connector\\MySqlPdo'
        ]
    ];

    // -----------------------------------------------------------------------------------------------------------------
    // Return the backend's configuration.
    // -----------------------------------------------------------------------------------------------------------------

    return $conf;
});

