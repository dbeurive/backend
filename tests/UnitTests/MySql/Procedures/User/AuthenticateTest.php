<?php

namespace dbeurive\BackendTests\UnitTests\MySql\Procedures\User;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Phpunit\PHPUnit_Backend_TestCase;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result;


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

        /** @var Result $result */

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, and password is valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $result = $procedure->execute([
            'user.login' => $user[0]['user.login'],
            'user.password' => $user[0]['user.password']
        ]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsNotEmpty($result);
        $this->assertResultValueSetIsNotEmpty($result);
        $this->assertResultValuesCount(1, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertResultDataSetCount(1, $result);

        // -----------------------------------------------------------------------------------------------------------------
        // Test: Login exists, and password is not valid.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT login as 'user.login', password as 'user.password' FROM user LIMIT 1", []);
        $result = $procedure->execute([
            'user.login'    => $user[0]['user.login'],
            'user.password' => $user[0]['user.password'] . '__'
        ]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertFalse($result->isError());
        $this->assertResultDataSetCount(0, $result);
    }
}