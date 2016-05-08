<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\Backend\Database\Entrypoints\Application\BaseResult;
use dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Link\AbstractLink;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Upsert extends AbstractApplication {

    // NOTE: The entry 'user.password' is duplicated. This is OK !!!!!
    private static $__upsertedFields = ['user.login', 'user.password', 'user.description', 'user.password'];
    private static $__sql = "INSERT INTO user
                             SET `user`.`login` = ?,
                                 `user`.`password` = ?,
                                 `user`.`description` = ?
                             ON DUPLICATE KEY UPDATE `user`.`password` = ?";

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
        if (! UtilArray::array_keys_exists(self::$__upsertedFields, $this->_executionConfig)) {
            $outErrorMessage = "Invalid SQL configuration. Mandatory fields are: " . implode(', ', self::$__upsertedFields) . "\nSee: " . __FILE__;
            return false;
        }
        return true;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _execute(array $inExecutionConfig, AbstractLink $inLink) {
        /* @var \PDO $pdo */
        $pdo = $inLink->getDatabaseConnexionHandler();

        // Execute the request.
        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__upsertedFields, $inExecutionConfig, true);
        $req = $pdo->prepare(self::$__sql);
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->_getSql(), true, true) . "\n" .
                "Condition fields: " .
                implode(', ', self::$__upsertedFields) . "\n" .
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
        $doc->setDescription('This request insert or update a user.')
            ->addEntityActionsRelationship(Entities::USER, Actions::UPSERT)
            ->setType($doc::TYPE_UPSERT)
            ->setSql(self::$__sql)
            ->addTable('user')
            ->setUpsertedFields(['user.login', 'user.password', 'user.description']);

        return $doc;
    }
}