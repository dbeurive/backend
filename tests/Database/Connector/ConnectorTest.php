<?php

namespace dbeurive\BackendTest\Database\Connector;
use dbeurive\BackendTest\Utils;

class ConnectorTest extends \PHPUnit_Framework_TestCase
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

        $connector = new \dbeurive\Backend\Database\Connector\MySqlPdo($this->__connectorMySqlConfiguration);
        $this->expectException(\Exception::class);
        $connector->getDatabaseHandler(); // The connexion is not established.
    }

    public function testGetConfiguration() {
        $connector = new \dbeurive\Backend\Database\Connector\MySqlPdo($this->__connectorMySqlConfiguration);
        $conf = $connector->getConfiguration();
        $this->assertTrue(is_array($conf));
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Test the specific MySql implementation.
    // -----------------------------------------------------------------------------------------------------------------

    public function testMysqlConnect() {

        $connector = new \dbeurive\Backend\Database\Connector\MySqlPdo($this->__connectorMySqlConfiguration);
        $connector->connect();
        /** @var \PDO $pdo */
        $pdo = $connector->getDatabaseHandler(); // The connexion is not established.

        Utils\Pdo::setPdo($pdo);
        $result = Utils\Pdo::select("SELECT COUNT(id) FROM user;");
        $this->assertCount(1, $result);
    }

    public function testMySqlGetConfiguration() {

        $connector = new \dbeurive\Backend\Database\Connector\MySqlPdo($this->__connectorMySqlConfiguration);
        $conf = $connector->getConfiguration();
        $this->assertTrue(is_array($conf));

        foreach ($connector->getConfigurationOptions() as $_option) {
            $this->assertTrue(is_array($_option));
            $options[] = $_option[\dbeurive\Backend\Database\Connector\MySqlPdo::OPTION_NAME];
        }

        $expected = [   \dbeurive\Backend\Database\Connector\MySqlPdo::DB_HOST,
                        \dbeurive\Backend\Database\Connector\MySqlPdo::DB_NAME,
                        \dbeurive\Backend\Database\Connector\MySqlPdo::DB_PASSWORD,
                        \dbeurive\Backend\Database\Connector\MySqlPdo::DB_PORT,
                        \dbeurive\Backend\Database\Connector\MySqlPdo::DB_USER];
        sort($expected);
        sort($options);

        $this->assertEquals(json_encode($expected), json_encode($options));
    }

}