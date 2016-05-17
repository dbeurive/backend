<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\BackendTest\EntryPoints\Result\BaseResult;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Entrypoints\AbstractSql;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Insert extends AbstractSql {

    private static $__insertedFields = ['user.login', 'user.password', 'user.description'];
    private $__sql = "INSERT INTO user
                      SET `user`.`login` = ?,
                          `user`.`password` = ?,
                          `user`.`description` = ?";


    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();

        // Execute the request.
        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__insertedFields, $inExecutionConfig, true);
        $req = $pdo->prepare($this->__sql);
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->__sql, true, true) . "\n" .
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

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
        $doc->setDescription('This request creates a user.')
            ->addEntityActionsRelationship(Entities::USER, Actions::CREATE)
            ->setType($doc::TYPE_INSERT)
            ->setSql($this->__sql)
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