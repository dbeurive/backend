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
class DeleteTest extends PHPUnit_Backend_TestCase
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

        $PROCEDURE_NAME = 'User/Delete';

        // Initialize the database.
        CliWriter::echoInfo("Loading " . __FILE__);

        /* var \dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User\Delete $procedure */
        $dataInterface = DatabaseInterface::getInstance();
        $procedure = $dataInterface->getProcedure($PROCEDURE_NAME);

        // -----------------------------------------------------------------------------------------------------------------
        // Test: user does not exist.
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'] + 1;
        $procedure->addInputField('user.id', $id)
            ->execute();

        $this->assertStatusIsOk($procedure);
        $this->assertResultDataSetIsEmpty($procedure);
        $this->assertResultValueSetIsEmpty($procedure);
        $this->assertResultValuesCount(0, $procedure);
        $this->assertResultDataSetCount(0, $procedure);
        $this->assertNull($procedure->getResult()->getErrorMessage());

        // -----------------------------------------------------------------------------------------------------------------
        // Test: user exists _AND_ has no profile !!!!!!!!
        // Remember that the last user has no profile !!!!!!!
        // -----------------------------------------------------------------------------------------------------------------

        $user = TestTools::select("SELECT max(user.id) as 'max' FROM user", []);
        $id = $user[0]['max'];
        echo "Delete user which ID is $id\n";
        $procedure->addInputField('user.id', $id)
            ->execute();

        $this->assertStatusIsOk($procedure);
        $this->assertResultDataSetIsEmpty($procedure);
        $this->assertResultValueSetIsEmpty($procedure);
        $this->assertResultValuesCount(0, $procedure);
        $this->assertResultDataSetCount(0, $procedure);
        $this->assertNull($procedure->getResult()->getErrorMessage());
    }
}