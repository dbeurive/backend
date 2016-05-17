<?php

namespace dbeurive\BackendTest\UnitTests\MySql\Sqls\User;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\BackendTest\EntryPoints\UnitTests\PhpUnit\PHPUnit_Backend_TestCase;
use dbeurive\BackendTest\EntryPoints\Result\SqlResult;

/**
 * @runTestsInSeparateProcesses
 */
class AuthenticateTest extends PHPUnit_Backend_TestCase
{
    use SetUp;

    protected function setUp() {
        // Drop and re-create all the databases.
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
        $this->__createDatabaseInterface();
        $this->__di->setDbHandler($this->__mySqlPdo);
    }

    public function testIt() {

        $REQ_NAME = 'User/Authenticate';
        CliWriter::echoInfo("Loading " . __FILE__);

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        /** @var SqlResult $result */

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login does not exist.
        // -----------------------------------------------------------------------------------------------------------------

        $result = $request->execute(['user.login' => 'toto', 'user.password' => 'titi']);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertNull($result->getErrorMessage());

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, but password is not valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $result = $request->execute(['user.login' => $user[0]['user.login'], 'user.password' => "{$user[0]['user.password']}___"]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertNull($result->getErrorMessage());

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, and password is valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $result = $request->execute(['user.login' => $user[0]['user.login'], 'user.password' => $user[0]['user.password']]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsNotEmpty($result);
        $this->assertResultDataSetCount(1, $result);
        $this->assertNull($result->getErrorMessage());
    }
}