<?php

namespace dbeurive\BackendTests\UnitTests\MySql\Sqls\User;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\Backend\Phpunit\PHPUnit_Backend_TestCase;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Lib\CliWriter;


/**
 * @runTestsInSeparateProcesses
 */
class DeleteTest extends PHPUnit_Backend_TestCase {

    use SetUp;

    protected function setUp() {
        // Drop and re-create all the databases.
        $this->__createMysqlDatabase();
        $this->__createLink('mysql', true);
        $this->__createDatabaseInterface();
        $this->__di->setDbLink($this->__link);
    }

    public function testIt()
    {
        $REQ_NAME = 'User/Delete';
        CliWriter::echoInfo("Loading " . __FILE__);

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        // -------------------------------------------------------------------------------------------------------------
        // Test: user's ID does not exists
        // -------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'] + 1;
        $request->setExecutionConfig(['user.id' => $id])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());

        // -------------------------------------------------------------------------------------------------------------
        // Test: delete a user that does note have any profile. It works.
        // -------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        $request->setExecutionConfig(['user.id' => $id])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());

        // -------------------------------------------------------------------------------------------------------------
        // Test: delete a user that has a profile. It does NOT works.
        // -------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        $request->setExecutionConfig(['user.id' => $id])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsNotOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertStringStartsWith('SQL request failed:', $request->getResult()->getErrorMessage());

        // -------------------------------------------------------------------------------------------------------------
        // Test: initial validation fails (the mandatory configuration is not defined).
        // -------------------------------------------------------------------------------------------------------------

        $this->expectException(\Exception::class);
        $request->setExecutionConfig([])
            ->execute();

        $this->assertHasNotBeenExecuted($request);

    }
}