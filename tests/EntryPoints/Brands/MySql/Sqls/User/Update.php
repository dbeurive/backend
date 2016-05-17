<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\BackendTest\EntryPoints\Result\BaseResult;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Entrypoints\AbstractSql;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Update extends AbstractSql {

    private static $__conditionFields = ['user.id'];
    private $__sql = "UPDATE user
                     SET __UPDATE__
                     WHERE  `user`.`id`=?";

    /**
     * Create the SQL request from the request's template.
     * @param array $inConfig Configuration.
     * @param \PDO $inPdo Handler to the database.
     * @return string The method returns a string that represents the SQL request.
     */
    private function __sql(array $inConfig, \PDO $inPdo) {
        $update = [];
        foreach ($inConfig as $_field => $_value) {
            if ('user.id' == $_field) {
                continue;
            }
            $update[] = $_field . " = " . $inPdo->quote($_value);
        }

        $update = implode(',', $update);
        $sql = str_replace('__UPDATE__', $update, $this->__sql);
        return $sql;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();
        $sql = $this->__sql($inExecutionConfig, $pdo);

        // Execute the request.
        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__conditionFields, $inExecutionConfig, true);
        $req = $pdo->prepare($sql);
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($sql, true, true) . "\n" .
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

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function getDescription() {

        $copy = $this;
        $sql = function() use ($copy) {
            $update = [];

            foreach ($copy->getTableFieldsNames('user') as $_fieldName) {
                if ('user.id' == $_fieldName) {
                    continue;
                }
                $update[] = $_fieldName . ' = <value>';
            }

            $update = implode(',', $update);
            $sql = str_replace('__UPDATE__', $update, $this->__sql);
            return $sql;
        };

        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
        $doc->setDescription('This request updates a user.')
            ->addEntityActionsRelationship(Entities::USER, Actions::UPDATE)
            ->setType($doc::TYPE_UPDATE)
            ->setSql($sql())
            ->addTable('user')
            ->setUpdatedFields(['user.login', 'user.password', 'user.description'])
            ->setConditionFields(self::$__conditionFields);

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