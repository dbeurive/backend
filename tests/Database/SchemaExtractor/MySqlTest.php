<?php

namespace dbeurive\Backend\Database\SchemaExtractor;

class MySqlTest extends \PHPUnit_Framework_TestCase
{
    use \dbeurive\BackendTest\SetUp;

    /** @var \dbeurive\Backend\Database\SchemaExtractor\MySql */
    private $__extractor = null;

    public function setUp() {
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
        $this->__createMySqlConnector(); // By default, the connection to the database is not established.
        $this->__connectorMySql->connect();
        $this->__extractor = new \dbeurive\Backend\Database\SchemaExtractor\MySql($this->__connectorMySql);
    }
    
    public function testGetDatabaseSchema() {
        $schema = $this->__extractor->getDatabaseSchema();
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

}