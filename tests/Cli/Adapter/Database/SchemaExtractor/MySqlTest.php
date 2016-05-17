<?php

namespace dbeurive\BackendTest\Cli\Adapter\Database\SchemaExtractor;
use dbeurive\Util\UtilUnitTest;

class MySqlTest extends \PHPUnit_Framework_TestCase
{
    use \dbeurive\BackendTest\SetUp;

    /** @var \dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\MySql */
    private $__extractor = null;

    public function setUp() {
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
        $this->__createMySqlConnector();
        $this->__extractor = new \dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\MySql($this->__mySqlConnector);
        $this->__mySqlConnector->connect();
    }

    public function testGetDatabaseSchema() {

        $schema = UtilUnitTest::call_private_or_protected_method(get_class($this->__extractor), '_getDatabaseSchema', $this->__extractor, $this->__mySqlConnector);
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