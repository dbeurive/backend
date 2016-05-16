<?php

namespace dbeurive\BackendTests\UnitTests\MySql\Procedures\User;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Phpunit\PHPUnit_Backend_TestCase;
use dbeurive\Backend\Cli\Lib\CliWriter;

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
        $this->__createMySqlConnector();
        $this->__connectorMySql->connect();
        $this->__createDatabaseInterface();
        $this->__di->setDbConnector($this->__connectorMySql);
    }

    public function testIt() {
        $PROCEDURE_NAME = 'User/Authenticate';

        // Initialize the database.
        CliWriter::echoInfo("Loading " . __FILE__);
        
        // Get the procedure.
        $dataInterface = DatabaseInterface::getInstance();
        /** @var \dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User\Authenticate $procedure */
        $procedure = $dataInterface->getProcedure($PROCEDURE_NAME);

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, and password is valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $procedure->setExecutionConfig([
            'user.login' => $user[0]['user.login'],
            'user.password' => $user[0]['user.password']
        ])->execute();

        $this->assertStatusIsOk($procedure);
        $this->assertResultDataSetIsNotEmpty($procedure);
        $this->assertResultValueSetIsNotEmpty($procedure);
        $this->assertResultValuesCount(1, $procedure);
        $this->assertTrue($procedure->isAuthorized());
        $this->assertResultDataSetCount(1, $procedure);

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, and password is not valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $procedure->setExecutionConfig([
            'user.login'    => $user[0]['user.login'],
            'user.password' => $user[0]['user.password'] . '__'
        ])->execute();

        $this->assertStatusIsOk($procedure);
        $this->assertResultDataSetIsEmpty($procedure);
        $this->assertFalse($procedure->isAuthorized());
        $this->assertResultDataSetCount(0, $procedure);
    }
}