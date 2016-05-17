<?php

namespace dbeurive\BackendTest\Cli\Adapter\Database\SchemaExtractor\Connector;
use dbeurive\BackendTest\Utils;

class MySqlPdoTest extends \PHPUnit_Framework_TestCase
{
    use \dbeurive\BackendTest\SetUp;

    public function setUp() {
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Test the base class.
    // -----------------------------------------------------------------------------------------------------------------

    public function testFailure() {

        $connector = new \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo($this->__mySqlConnectorConfiguration);
        $this->expectException(\Exception::class);
        $connector->getDatabaseHandler(); // The connexion is not established.
    }

    public function testGetConfiguration() {
        $connector = new \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo($this->__mySqlConnectorConfiguration);
        $conf = $connector->getConfiguration();
        $this->assertTrue(is_array($conf));
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Test the specific MySql implementation.
    // -----------------------------------------------------------------------------------------------------------------

    public function testMysqlConnect() {

        $connector = new \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo($this->__mySqlConnectorConfiguration);
        $connector->connect();
        /** @var \PDO $pdo */
        $pdo = $connector->getDatabaseHandler(); // The connexion is not established.

        Utils\Pdo::setPdo($pdo);
        $result = Utils\Pdo::select("SELECT COUNT(id) FROM user;");
        $this->assertCount(1, $result);
    }

    public function testMySqlGetConfiguration() {

        $connector = new \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo($this->__mySqlConnectorConfiguration);
        $conf = $connector->getConfiguration();
        $this->assertTrue(is_array($conf));

        foreach ($connector->getConfigurationParameters() as $_option) {
            $this->assertTrue(is_array($_option));
            $options[] = $_option[\dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo::OPTION_NAME];
        }

        $expected = [   \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo::DB_HOST,
                        \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo::DB_NAME,
                        \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo::DB_PASSWORD,
                        \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo::DB_PORT,
                        \dbeurive\Backend\Cli\Adapter\Database\Connector\MySqlPdo::DB_USER];
        sort($expected);
        sort($options);

        $this->assertEquals(json_encode($expected), json_encode($options));
    }

}