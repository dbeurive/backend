<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\Backend\Database\Entrypoints\Application\BaseResult;
use dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Connector\AbstractConnector;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Insert extends AbstractApplication {

    private static $__insertedFields = ['user.login', 'user.password', 'user.description'];
    private static $__sql = "INSERT INTO user
                             SET `user`.`login` = ?,
                                 `user`.`password` = ?,
                                 `user`.`description` = ?";

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function _init(array $inInitConfig=[]) {
        $this->_setSql(self::$__sql);
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) {
        // Make sure that we have all the fields used within the clause "WHERE" of the SQL request.
        if (! UtilArray::array_keys_exists(self::$__insertedFields, $this->_executionConfig)) {
            $outErrorMessage = "Invalid SQL configuration. Mandatory fields are: " . implode(', ', self::$__insertedFields) . "\nSee: " . __FILE__;
            return false;
        }
        return true;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector) {
        /* @var \PDO $pdo */
        $pdo = $inConnector->getDatabaseHandler();

        // Execute the request.
        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__insertedFields, $inExecutionConfig, true);
        $req = $pdo->prepare(self::$__sql);
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->_getSql(), true, true) . "\n" .
                "Condition fields: " .
                implode(', ', self::$__insertedFields) . "\n" .
                "Bound to values: " .
                implode(', ', $fieldsValues);
            $result->setErrorMessage($message);
            return $result;
        }

        $result->setStatusSuccess();
        return $result;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Mandatory static methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
        $doc->setDescription('This request creates a user.')
            ->addEntityActionsRelationship(Entities::USER, Actions::CREATE)
            ->setType($doc::TYPE_INSERT)
            ->setSql(self::$__sql)
            ->addTable('user')
            ->setInsertedFields(['user.login', 'user.password', 'user.description']);

        // Note that the following methods are not called:
        // - addTags
        // - addOutputDataValue
        // - setTables
        // - addSelectedField
        // - setSelectedFields
        // - setUpdatedFields
        // - addConditionField

        return $doc;
    }
}