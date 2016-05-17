<?php

namespace dbeurive\BackendTest\UnitTests\MySql\Sqls\User;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\BackendTest\EntryPoints\UnitTests\PhpUnit\PHPUnit_Backend_TestCase;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\BackendTest\EntryPoints\Result\SqlResult;


/**
 * @runTestsInSeparateProcesses
 */
class DeleteTest extends PHPUnit_Backend_TestCase {

    use SetUp;

    protected function setUp()
    {
        // Drop and re-create all the databases.
        CliWriter::echoInfo("Loading " . __FILE__);
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
        $this->__createDatabaseInterface();
        $this->__di->setDbHandler($this->__mySqlPdo);
    }

    public function testIt()
    {
        $REQ_NAME = 'User/Delete';

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        /** @var SqlResult $result */

        // -------------------------------------------------------------------------------------------------------------
        // Test: user's ID does not exists
        // -------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'] + 1;
        $result = $request->execute(['user.id' => $id]);

        print_r($result->getErrorMessage());

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
        // Test: initial validation fails (the mandatory configuration is not defined).
        // -------------------------------------------------------------------------------------------------------------

        error_reporting(E_ERROR | E_PARSE);
        $result = $request->execute([]);
        error_reporting(E_WARNING | E_ERROR | E_PARSE);
        $this->assertFalse($result->isSuccess());

        // -------------------------------------------------------------------------------------------------------------
        // Test: delete a user that has a profile. It does NOT works.
        // -------------------------------------------------------------------------------------------------------------

        $this->expectException(\PDOException::class);
        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];

        // error_reporting(E_ERROR | E_PARSE);
        $request->execute(['user.id' => $id]);
        // error_reporting(E_WARNING | E_ERROR | E_PARSE);

    }
}