<?php

namespace dbeurive\BackendTests\UnitTests\MySql\Sqls\User;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Backend\Phpunit\PHPUnit_Backend_TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class AuthenticateTest extends PHPUnit_Backend_TestCase
{
    use SetUp;

    protected function setUp() {
        // Drop and re-create all the databases.
        $this->__createMysqlDatabase();
        $this->__createLink('mysql', true);
        $this->__createDatabaseInterface();
        $this->__di->setDbLink($this->__link);
    }

    public function testIt() {

        $REQ_NAME = 'User/Authenticate';
        CliWriter::echoInfo("Loading " . __FILE__);
        

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login does not exist.
        // -----------------------------------------------------------------------------------------------------------------

        $request->setExecutionConfig(['user.login' => 'toto', 'user.password' => 'titi'])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, but password is not valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $request->setExecutionConfig(['user.login' => $user[0]['user.login'], 'user.password' => "{$user[0]['user.password']}___"])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, and password is valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $request->setExecutionConfig(['user.login' => $user[0]['user.login'], 'user.password' => $user[0]['user.password']])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsNotEmpty($request);
        $this->assertResultDataSetCount(1, $request);
        $this->assertNull($request->getResult()->getErrorMessage());
    }

}