<?php

namespace dbeurive\BackendTests\UnitTests\MySql\Sqls\User;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\Backend\Phpunit\PHPUnit_Backend_TestCase;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Backend\Database\Entrypoints\Application\Sql\Result;


/**
 * @runTestsInSeparateProcesses
 */
class DeleteTest extends PHPUnit_Backend_TestCase {

    use SetUp;

    protected function setUp()
    {
        // Drop and re-create all the databases.
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
        $this->__createMySqlConnector();
        $this->__connectorMySql->connect();
        $this->__createDatabaseInterface();
        $this->__di->setDbConnector($this->__connectorMySql);
    }

    public function testIt()
    {
        $REQ_NAME = 'User/Delete';
        CliWriter::echoInfo("Loading " . __FILE__);

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        /** @var Result $result */

        // -------------------------------------------------------------------------------------------------------------
        // Test: user's ID does not exists
        // -------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'] + 1;
        $result = $request->execute(['user.id' => $id]);


        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertNull($result->getErrorMessage());

        // -------------------------------------------------------------------------------------------------------------
        // Test: delete a user that does note have any profile. It works.
        // -------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        $result = $request->execute(['user.id' => $id]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertNull($result->getErrorMessage());

        // -------------------------------------------------------------------------------------------------------------
        // Test: delete a user that has a profile. It does NOT works.
        // -------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        $result = $request->execute(['user.id' => $id]);

        $this->assertStatusIsNotOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertStringStartsWith('SQL request failed:', $result->getErrorMessage());

        // -------------------------------------------------------------------------------------------------------------
        // Test: initial validation fails (the mandatory configuration is not defined).
        // -------------------------------------------------------------------------------------------------------------

        error_reporting(E_ERROR | E_PARSE);
        $result = $request->execute([]);
        $this->assertFalse($result->isSuccess());
        error_reporting(E_WARNING | E_ERROR | E_PARSE);
    }
}