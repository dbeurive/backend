<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\BackendTest\EntryPoints\Result\BaseResult;
use dbeurive\Backend\Database\EntryPoints\Description;
use dbeurive\Backend\Database\EntryPoints\AbstractSql;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;
use dbeurive\Util\UtilSql\MySql as UtilMySql;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Select extends AbstractSql {

    const KEY_LIMIT_FROM  = 'limit_from';
    const KEY_LIMIT_COUNT = 'limit_count';
    private static $__pdoParams = [self::KEY_LIMIT_FROM, self::KEY_LIMIT_COUNT];
    private $__sql = "SELECT user.* FROM user LIMIT ?,?";

    /**
     * Create the SQL request from the request's template.
     * @return string The method returns a string that represents the SQL request.
     */
    private function __getSql() {
        return UtilMySql::developSql($this->__sql, $this->getDatabaseSchema(), true, true);
    }

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();

        // Execute the request.
        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__pdoParams, $inExecutionConfig, true);
        $req = $pdo->prepare($this->__getSql());
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->__getSql(), true, true) . "\n" .
                "Condition fields: " .
                implode(', ', self::$__pdoParams) . "\n" .
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
        $doc->setDescription('This request selects a batch of users')
            ->addEntityActionsRelationship(Entities::USER, Actions::SELECT)
            ->addParameterValue(self::KEY_LIMIT_FROM, "Start value for the selection of users")
            ->addParameterValue(self::KEY_LIMIT_COUNT, "Limit the number of selected users")
            ->setType($doc::TYPE_SELECT)
            ->setSql($this->__getSql())
            ->addTable('user')
            ->setSelectedFields(['user.*']);

        return $doc;
    }

}