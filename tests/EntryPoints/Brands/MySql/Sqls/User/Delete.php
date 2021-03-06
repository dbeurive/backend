<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\BackendTest\EntryPoints\Result\BaseResult;
use dbeurive\Backend\Database\EntryPoints\Description;
use dbeurive\Backend\Database\EntryPoints\AbstractSql;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;


class Delete extends AbstractSql {

    private static $__conditionFields = ['user.id'];
    private $__sql = "DELETE
                      FROM   user
                      WHERE  `user`.`id`=?";


    /**
     * @see \dbeurive\BackendTest\EntryPoints\Result\AbstractApplication
     */
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();

        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__conditionFields, $inExecutionConfig, true);

        // Validate the configuration prior to the execution.
        if (count($fieldsValues) != count(self::$__conditionFields)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->__sql, true, true) . "\n" .
                "Condition fields: " .
                implode(', ', self::$__conditionFields) . "\n" .
                "Bound to values: " .
                implode(', ', $fieldsValues);
            $result->setErrorMessage($message);
            return $result;
        }

        $req = $pdo->prepare($this->__sql);
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->__sql, true, true) . "\n" .
                "Condition fields: " .
                implode(', ', self::$__conditionFields) . "\n" .
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
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\EntryPoints\Description\Sql();
        $doc->setDescription('This request delete a user.')
            ->addEntityActionsRelationship(Entities::USER, Actions::DELETE)
            ->setType($doc::TYPE_DELETE)
            ->setSql($this->__sql)
            ->addTable('user')
            ->setConditionFields(self::$__conditionFields);

        // Note that the following methods are not called:
        // - addTags
        // - addOutputDataValue
        // - setTables
        // - addSelectedField
        // - setSelectedFields
        // - addUpdatedField
        // - setUpdatedFields
        // - addConditionField

        return $doc;
    }
}