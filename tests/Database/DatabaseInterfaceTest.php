<?php

namespace dbeurive\BackendTest\Database;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Database\Doc\ConfigurationParameter as DocOption;
use dbeurive\Backend\Database\Connector\ConfigurationParameter as ConnectorOption;

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

    public function testGetTableFieldsNames() {

        // Load the schema of the database.
        $reference = require $this->__generalConfiguration['application'][DocOption::SCHEMA_PATH];
        $reference = $reference['user'];

        $this->__di->setDbHandler($this->__mySqlPdo);

        $fields = $this->__di->getTableFieldsNames('user');
        $this->assertCount(0, array_diff($reference, $fields));
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //    TODO: Add unit tests for the entry point provider
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
}