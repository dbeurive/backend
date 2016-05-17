<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\BackendTest\EntryPoints\Result\BaseResult;
use dbeurive\Backend\Database\EntryPoints\Description;
use dbeurive\Backend\Database\EntryPoints\AbstractSql;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Upsert extends AbstractSql {

    // NOTE: The entry 'user.password' is duplicated. This is OK !!!!!
    private static $__upsertedFields = ['user.login', 'user.password', 'user.description', 'user.password'];
    private $__sql = "INSERT INTO user
                      SET `user`.`login` = ?,
                          `user`.`password` = ?,
                          `user`.`description` = ?
                      ON DUPLICATE KEY UPDATE `user`.`password` = ?";

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();

        // Execute the request.
        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__upsertedFields, $inExecutionConfig, true);
        $req = $pdo->prepare($this->__sql);
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->__sql, true, true) . "\n" .
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

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\EntryPoints\Description\Sql();
        $doc->setDescription('This request insert or update a user.')
            ->addEntityActionsRelationship(Entities::USER, Actions::UPSERT)
            ->setType($doc::TYPE_UPSERT)
            ->setSql($this->__sql)
            ->addTable('user')
            ->setUpsertedFields(['user.login', 'user.password', 'user.description']);

        return $doc;
    }
}