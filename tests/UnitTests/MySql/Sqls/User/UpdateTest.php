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
class UpdateTest extends PHPUnit_Backend_TestCase
{
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
        $REQ_NAME = 'User/Update';
        CliWriter::echoInfo("Loading " . __FILE__);

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        // -----------------------------------------------------------------------------------------------------------------
        // Update a password.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        $request->setExecutionConfig(['user.id' => $id, 'user.password' => "New Password!"])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());

        $res = TestTools::select("SELECT user.password as 'user.password' FROM user WHERE id=$id", []);
        $password = $res[0]['user.password'];
        $this->assertEquals('New Password!', $password);
    }
}