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
class UpdateTest extends PHPUnit_Backend_TestCase
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

    public function testIt()
    {
        $REQ_NAME = 'User/Update';
        CliWriter::echoInfo("Loading " . __FILE__);

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        /** @var SqlResult $result */

        // -----------------------------------------------------------------------------------------------------------------
        // Update a password.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        $result = $request->execute(['user.id' => $id, 'user.password' => "New Password!"]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertNull($result->getErrorMessage());

        $res = TestTools::select("SELECT user.password as 'user.password' FROM user WHERE id=$id", []);
        $password = $res[0]['user.password'];
        $this->assertEquals('New Password!', $password);
    }
}