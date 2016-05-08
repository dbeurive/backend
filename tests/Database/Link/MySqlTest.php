<?php

namespace dbeurive\BackendTest\Database\Link;
use dbeurive\Backend\Database\Link;

/**
 * @runTestsInSeparateProcesses
 */
class MySqlTest extends \PHPUnit_Framework_TestCase
{
    use \dbeurive\BackendTest\SetUp;

    public function setUp() {
        // print "\nExecuting " . __METHOD__ . "\n";
        $this->__createMysqlDatabase();
        /** @var \dbeurive\Backend\Database\Link\MySql */
        $this->__link = new Link\MySql();
    }

    public function testGetConfigurationOptions() {
        $options = [];

        foreach ($this->__link->getConfigurationOptions() as $_option) {
            $this->assertTrue(is_array($_option));
            $options[] = $_option[Link\MySql::OPTION_NAME];
        }

        $expected = [Link\MySql::DB_HOST, Link\MySql::DB_NAME, Link\MySql::DB_PASSWORD, Link\MySql::DB_PORT, Link\MySql::DB_USER];
        sort($expected);
        sort($options);

        $this->assertEquals(json_encode($expected), json_encode($options));
    }

    public function testConnect() {
        $status = $this->__link->setConfiguration($this->__linkConfiguration);
        $this->assertTrue(is_array($status));
        $this->assertCount(0, $status);

        $status = $this->__link->connect();
        $this->assertTrue($status);
    }

    public function testQuoteValue() {
        $value = "10";
        $this->__link->setConfiguration($this->__linkConfiguration);
        $this->__link->connect();
        $this->assertEquals("'$value'", $this->__link->quoteValue($value));
    }

    public function testQuoteFieldName() {
        $fieldName = 'user.id';
        $this->assertEquals('`user`.`id`', $this->__link->quoteFieldName($fieldName));
    }

    public function testGetDatabaseSchema() {
        $this->__link->setConfiguration($this->__linkConfiguration);
        $this->__link->connect();
        $schema = $this->__link->getDatabaseSchema();
        $this->assertTrue(is_array($schema));

        /**
         * @var array $schema
         */
        $sortedSchema = [];
        $keys = array_keys($schema);
        sort($keys);
        foreach ($keys as $_key) {
            $fields = $schema[$_key];
            sort($fields);
            $sortedSchema[$_key] = $fields;
        }

        $referencePath = $this->__generalConfiguration['test']['dir.references'] . DIRECTORY_SEPARATOR . 'mysql_db_schema.json';
        $this->assertJsonStringEqualsJsonFile($referencePath, json_encode($sortedSchema));
    }

    public function testSetDatabaseConnexionHandler() {
        $this->__link->setDatabaseConnexionHandler($this->__pdo);

        // Now quote a value, just to make sure that everything is fine.
        $this->assertEquals("'10'", $this->__link->quoteValue(10));
    }
}