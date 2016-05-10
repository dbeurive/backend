<?php

namespace dbeurive\BackendTests\UnitTests\MySql\Sqls\Profile;

use dbeurive\BackendTest\SetUp;
use dbeurive\BackendTest\Utils\Pdo as TestTools;

use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Backend\Phpunit\PHPUnit_Backend_TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class GetTest extends PHPUnit_Backend_TestCase
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

    public function testIt()
    {
        $REQ_NAME = 'Profile/Get';
        CliWriter::echoInfo("Loading " . __FILE__);

        // Initialize the database.
        CliWriter::echoInfo("Loaded");

        // Get the SQL request.
        $dataInterface = DatabaseInterface::getInstance();
        $request = $dataInterface->getSql($REQ_NAME);

        // -----------------------------------------------------------------------------------------------------------------
        // Test: profile does not exist (no user associated).
        // -----------------------------------------------------------------------------------------------------------------

        $res = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $res[0]['max'] + 1;
        $request->setExecutionConfig(['profile.fk_user_id' => $id])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());

        // -----------------------------------------------------------------------------------------------------------------
        // Test: profile exists.
        // -----------------------------------------------------------------------------------------------------------------

        $res = TestTools::select("SELECT max(profile.fk_user_id) as 'max' FROM profile", []);
        $id = $res[0]['max'];
        $request->setExecutionConfig(['profile.fk_user_id' => $id])
            ->execute();

        $this->assertHasBeenExecuted($request);
        $this->assertStatusIsOk($request);
        $this->assertResultDataSetIsNotEmpty($request);
        $this->assertNull($request->getResult()->getErrorMessage());
        $this->assertResultDataSetCount(1, $request);
    }
}