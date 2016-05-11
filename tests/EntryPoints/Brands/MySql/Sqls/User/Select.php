<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\Backend\Database\Entrypoints\Application\BaseResult;
use dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Connector\AbstractConnector;
use dbeurive\Backend\Database\SqlService\InterfaceSqlService as SqlService;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Select extends AbstractApplication {

    const KEY_LIMIT_FROM  = 'limit_from';
    const KEY_LIMIT_COUNT = 'limit_count';
    private static $__pdoParams = [self::KEY_LIMIT_FROM, self::KEY_LIMIT_COUNT];
    private static $__sql = "SELECT __USER__ FROM user LIMIT ?,?";

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function _init(array $inInitConfig=[]) {
        $this->_setSql($this->__getSql());
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) {
        if (! UtilArray::array_keys_exists(self::$__pdoParams, $this->_executionConfig)) {
            $outErrorMessage = "Invalid SQL configuration. Mandatory parameters are: " . implode(', ', self::$__pdoParams) . "\nSee: " . __FILE__;
            return false;
        }
        return true;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector, SqlService $inSqlService) {
        /* @var \PDO $pdo */
        $pdo = $inConnector->getDatabaseHandler();

        // Execute the request.
        $result = new BaseResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__pdoParams, $inExecutionConfig, true);
        $req = $pdo->prepare(self::$__sql);
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed:\n" .
                UtilString::text_linearize($this->_getSql(), true, true) . "\n" .
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

    // -----------------------------------------------------------------------------------------------------------------
    // Mandatory static methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
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

    // -----------------------------------------------------------------------------------------------------------------
    // Private methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Create the SQL request from the request's template.
     * @return string The method returns a string that represents the SQL request.
     */
    private function __getSql() {
        $sql = preg_replace('/__USER__/', $this->_getTableFieldsNames('user', self::FIELDS_FULLY_QUALIFIED_AS_SQL), self::$__sql);
        return $sql;
    }
}