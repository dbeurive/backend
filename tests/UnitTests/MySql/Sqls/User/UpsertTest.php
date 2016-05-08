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
class UpsertTest extends PHPUnit_Backend_TestCase
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
        $REQ_NAME = 'User/Upsert';
        CliWriter::echoInfo("Loading " . __FILE__);

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        // -----------------------------------------------------------------------------------------------------------------
        // Update a password.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT * FROM user ORDER BY id LIMIT 1", []);
        $id = $user[0]['id'];
        $login = $user[0]['login'];
        $password = $user[0]['password'];
        $description = $user[0]['description'];

        $request->setExecutionConfig(['user.login' => $login, 'user.password' => "New $password!", 'user.description' => $description])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());

        $res = TestTools::select("SELECT user.password as 'user.password' FROM user WHERE id=$id", []);
        $newPassword = $res[0]['user.password'];
        $this->assertEquals("New $password!", $newPassword);
    }
}