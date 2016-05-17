<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\Profile;

use dbeurive\BackendTest\EntryPoints\Result\SqlResult;
use dbeurive\Backend\Database\EntryPoints\Description;
use dbeurive\Backend\Database\SqlService\MySql;
use dbeurive\Backend\Database\EntryPoints\AbstractSql;
use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;
use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Get extends AbstractSql {

    private static $__conditionFields = ['profile.fk_user_id'];
    private $__sql = "SELECT     __PROFILE__
                      FROM       profile
                      WHERE      `profile`.`fk_user_id`=?
                      ORDER BY   `profile`.`id`";

    /**
     * Create the SQL request from the request's template.
     * @return string The method returns a string that represents the SQL request.
     */
    private function __sql() {
        $profile = MySql::getFullyQualifiedFieldsAsSql('profile', $this->getTableFieldsNames('profile'));
        return str_replace('__PROFILE__', $profile, $this->__sql);
    }

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();
        $sql = $this->__sql();
        $result = new SqlResult();
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

        $result->setDataSets($req->fetchAll(\PDO::FETCH_ASSOC));
        return $result;
    }

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function getDescription() {

        $doc = new \dbeurive\Backend\Database\EntryPoints\Description\Sql();
        $doc->setDescription('This request delete a user.')
            ->addEntityActionsRelationship(Entities::USER_PROFILE, Actions::SELECT)
            ->setType($doc::TYPE_SELECT)
            ->setSql($this->__sql())
            ->addTable('profile')
            ->setConditionFields(self::$__conditionFields)
            ->addPresentationField('profile.id')
            ->addSelectedField('profile.*');

        // Note that the following methods are not called:
        // - addTags
        // - addOutputDataValue
        // - setTables
        // - setSelectedFields
        // - addUpdatedField
        // - setUpdatedFields
        // - addConditionField

        return $doc;
    }
}