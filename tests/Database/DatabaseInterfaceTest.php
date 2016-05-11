<?php

namespace dbeurive\BackendTest\Database;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Database\Doc\Option as DocOption;
use dbeurive\Backend\Database\Connector\Option as ConnectorOption;

/**
 * @runTestsInSeparateProcesses
 */
class DatabaseInterfaceTest extends \PHPUnit_Framework_TestCase
{
    use \dbeurive\BackendTest\SetUp;

    public function setUp() {
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
        $this->__createDatabaseInterface();
        // No link to the database is created.
    }

    public function testGetInstance() {
        $this->assertInstanceOf('\dbeurive\Backend\Database\DatabaseInterface', $this->__di->getInstance());
    }

    public function testGetDbLinkOnError() {
        $this->expectException(\Exception::class);
        $this->__di->getDbConnector(); // The link has not been set.
    }

    public function testGetEntryPointProvider() {
        $this->assertInstanceOf('\dbeurive\Backend\Database\Entrypoints\Provider', $this->__di->getEntryPointProvider());
    }

    public function testGetDatabaseSchema() {
        // The path to the schema has been defined through the configuration.
        $this->__di->getDatabaseSchema();
    }

    public function testSetPhpDatabaseRepresentationPath() {
        // We will specify a path to a file that does not exist.
        $referencePath = $this->__generalConfiguration['application'][DocOption::SCHEMA_PATH] . '__';
        $this->assertFalse($this->__di->setPhpDatabaseRepresentationPath($referencePath));

        // We will specify a path to a file that does exist.
        $referencePath = $this->__generalConfiguration['application'][DocOption::SCHEMA_PATH];
        $this->assertTrue($this->__di->setPhpDatabaseRepresentationPath($referencePath));
    }

    public function testGetTableFieldsNamesOnError() {
        $this->expectException(\Exception::class);
        // No link to the database has been assigned.
        // Therefore, it is impossible to quote fields.
        $this->__di->getTableFieldsNames('user', DatabaseInterface::FIELDS_RAW_AS_ARRAY, true);
    }


    public function testGetTableFieldsNames() {

        // Load the schema of the database.
        $reference = require $this->__generalConfiguration['application'][DocOption::SCHEMA_PATH];
        $reference = $reference['user'];

        // Create a link to the database, but do not establish the connexion.
        $this->__createConnector('mysql');
        $this->__di->setDbConnector($this->__connector);

        $fields = $this->__di->getTableFieldsNames('user', DatabaseInterface::FIELDS_RAW_AS_ARRAY, false);
        $this->assertCount(0, array_diff($reference, $fields));

        $fields = $this->__di->getTableFieldsNames('user', DatabaseInterface::FIELDS_RAW_AS_ARRAY, true);
        $this->assertCount(0, array_diff($reference, $fields));

        $fields = $this->__di->getTableFieldsNames('user', DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_ARRAY, false);
        $ref = array_map(function($e) { return 'user.' . $e; }, $reference);
        $this->assertCount(0, array_diff($ref, $fields));

        $fields = $this->__di->getTableFieldsNames('user', DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_ARRAY, true);
        $ref = array_map(function($e) { return '`user`.' . "`$e`"; }, $reference);
        $this->assertCount(0, array_diff($ref, $fields));

        $fields = $this->__di->getTableFieldsNames('user', DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_SQL, false);
        $fields = explode(', ', $fields);
        $ref = array_map(function($e) { return 'user.' . $e . " as 'user." . $e . "'"; }, $reference);
        $this->assertCount(0, array_diff($ref, $fields));

        $fields = $this->__di->getTableFieldsNames('user', DatabaseInterface::FIELDS_FULLY_QUALIFIED_AS_SQL, true);
        $fields = explode(', ', $fields);
        $ref = array_map(function($e) { return '`user`.`' . $e . "` as 'user." . $e . "'"; }, $reference);
        $this->assertCount(0, array_diff($ref, $fields));
    }

    /**
     * Create the link to the database from a given database brand name.
     * @param string $inDbName Database brand name ("mysql").
     * @param bool $inOptConnect This flag indicates whether the link should open a connection to the database or not.
     *        * This the value of this parameter is true, then the link is created, and the connexion to the database is established.
     *        * Otherwise, the link is created, but the connexion to the database is not established.
     * @throws \Exception
     */
    public function __createConnector($inDbName, $inOptConnect=false)
    {
        $this->__init();

        // -------------------------------------------------------------------------------------------------------------
        // Initialise the connector to the database.
        // -------------------------------------------------------------------------------------------------------------

        $connectorName = $this->__generalConfiguration[$inDbName][ConnectorOption::CONNECTOR_NAME];
        $connectorConf = $this->__generalConfiguration[$inDbName][ConnectorOption::CONNECTOR_CONFIG];

        $this->__connector = new $connectorName($connectorConf);

        if ($inOptConnect) {
            $this->__connector->connect();
        }
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //    TODO: Add unit tests for the entry point provider
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
}