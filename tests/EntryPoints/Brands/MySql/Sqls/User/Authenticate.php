<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;

use dbeurive\BackendTest\EntryPoints\Result\SqlResult;
use dbeurive\Backend\Database\EntryPoints\Description;
use dbeurive\Backend\Database\EntryPoints\AbstractSql;


use dbeurive\BackendTest\EntryPoints\Constants\Tags;
use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;
use dbeurive\BackendTest\EntryPoints\Constants\OutputValues;


use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;
use dbeurive\Util\UtilSql\MySql as UtilMySql;


/**
 * Class Authenticate
 * @package dbeurive\BackendTest\Database\EntryPoints\Sql\User
 */

class Authenticate extends AbstractSql
{
    private static $__conditionFields = ['user.login', 'user.password'];
    private $__sql = "SELECT user.*,
                             1 as '__V__'
                      FROM   user
                      WHERE  `user`.`login`=?
                        AND  `user`.`password`=?";


    /**
     * Create the SQL request from the request's template.
     * @return string The method returns a string that represents the SQL request.
     */
    private function __sql() {
        $sql = UtilMySql::developSql($this->__sql, $this->getDatabaseSchema(), true, true);
        return str_replace('__V__', OutputValues::OUTPUT_VALUE_IS_AUTHENTICATED, $sql);
    }

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();

        $result = new SqlResult();
        $fieldsValues = UtilArray::array_keep_keys(self::$__conditionFields, $inExecutionConfig, true);
        $req = $pdo->prepare($this->__sql());
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

        $result->setDataSets($req->fetchAll(\PDO::FETCH_ASSOC));
        return $result;
    }

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function getDescription() {

        $doc = new \dbeurive\Backend\Database\EntryPoints\Description\Sql();
        $doc->setDescription('This request checks that the authentication data is valid.')
            ->addTags(Tags::AUTHENTICATION)
            ->addOutputDataValue(OutputValues::OUTPUT_VALUE_IS_AUTHENTICATED, 'This value indicates whether the user is authenticated or not.')
            ->addEntityActionsRelationship(Entities::USER, Actions::SELECT)
            ->setType($doc::TYPE_SELECT)
            ->setSql($this->__sql())
            ->addTable('user')
            ->setSelectedFields(['user.*']) // <=> ->setSelectedFields($this->_getTableFieldsNames('user', self::FIELDS_FULLY_QUALIFIED_AS_ARRAY, false))
            ->setConditionFields(self::$__conditionFields);

        // Note that the following methods are not called:
        // - setTables
        // - addSelectedField
        // - addUpdatedField
        // - setUpdatedFields
        // - addConditionField

        return $doc;
    }
}

