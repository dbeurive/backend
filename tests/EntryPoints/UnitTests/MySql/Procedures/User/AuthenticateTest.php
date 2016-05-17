<?php

namespace dbeurive\BackendTest\UnitTests\MySql\Procedures\User;



use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;
use dbeurive\BackendTest\EntryPoints\Result\ProcedureResult;
use dbeurive\BackendTest\EntryPoints\UnitTests\PhpUnit\PHPUnit_Backend_TestCase;

use dbeurive\Backend\Database\DatabaseInterface;
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
        $this->__createDatabaseInterface();
        $this->__di->setDbHandler($this->__mySqlPdo);
    }

    public function testIt() {
        $PROCEDURE_NAME = 'User/Authenticate';

        // Initialize the database.
        CliWriter::echoInfo("Loading " . __FILE__);
        
        // Get the procedure.
        $dataInterface = DatabaseInterface::getInstance();
        /** @var \dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User\Authenticate $procedure */
        $procedure = $dataInterface->getProcedure($PROCEDURE_NAME);

        /** @var ProcedureResult $result */

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