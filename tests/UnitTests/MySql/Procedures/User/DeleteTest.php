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
class DeleteTest extends PHPUnit_Backend_TestCase
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

        $PROCEDURE_NAME = 'User/Delete';

        // Initialize the database.
        CliWriter::echoInfo("Loading " . __FILE__);

        /* var \dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User\Delete $procedure */
        $dataInterface = DatabaseInterface::getInstance();
        $procedure = $dataInterface->getProcedure($PROCEDURE_NAME);

        /** @var Result $result */

        // -----------------------------------------------------------------------------------------------------------------
        // Test: user does not exist.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'] + 1;
        $result = $procedure->execute([
            'user.id' => $id
        ]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertResultValueSetIsEmpty($result);
        $this->assertResultValuesCount(0, $result);
        $this->assertResultDataSetCount(0, $result);
        $this->assertNull($result->getErrorMessage());

        // -----------------------------------------------------------------------------------------------------------------
        // Test: user exists _AND_ has no profile !!!!!!!!
        // Remember that the last user has no profile !!!!!!!
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        $result = $procedure->execute([
            'user.id' => $id
        ]);

        $this->assertStatusIsOk($result);
        $this->assertResultDataSetIsEmpty($result);
        $this->assertResultValueSetIsEmpty($result);
        $this->assertResultValuesCount(0, $result);
        $this->assertResultDataSetCount(0, $result);
        $this->assertNull($result->getErrorMessage());
    }
}