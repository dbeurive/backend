<?php

namespace dbeurive\BackendTest\Database\Doc;

use dbeurive\Backend\Database\Doc\Writer;

// @runTestsInSeparateProcesses

class WriterTest extends \PHPUnit_Framework_TestCase
{
    use \dbeurive\BackendTest\SetUp;

    public function setUp() {
        // print "\nExecuting " . __METHOD__ . "\n";
        $this->__createMysqlDatabase();
        // No link to the database is created.
    }

    public function testWriter() {
        $config = array_merge($this->__generalConfiguration['application'], $this->__generalConfiguration['mysql']);
        Writer::writer($config);
    }

}