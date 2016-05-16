<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\Profile;

use dbeurive\Backend\Database\Entrypoints\Application\Sql\Result;
use dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Connector\AbstractConnector;
use dbeurive\Backend\Database\SqlService\MySql;

use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\Util\UtilArray;
use dbeurive\Util\UtilString;

class Get extends AbstractApplication {

    private static $__conditionFields = ['profile.fk_user_id'];
    private static $__sql = "SELECT     __PROFILE__
                             FROM       profile
                             WHERE      `profile`.`fk_user_id`=?
                             ORDER BY   `profile`.`id`";

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function _init($inInitConfig=null) {
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _validateExecutionConfig($inExecutionConfig, &$outErrorMessage) {
        // Make sure that we have all the fields used within the clause "WHERE" of the SQL request.
        /** @var array $inExecutionConfig */
        if (! UtilArray::array_keys_exists(self::$__conditionFields, $this->_executionConfig)) {
            $outErrorMessage = "Invalid SQL configuration. Mandatory fields are: " . implode(', ', self::$__conditionFields) . "\nSee: " . __FILE__;
            return false;
        }
        return true;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _execute($inExecutionConfig, AbstractConnector $inConnector) {
        /* @var \PDO $pdo */
        $pdo = $inConnector->getDatabaseHandler();

        $sql = self::$__sql;

        $profile = MySql::getFullyQualifiedFieldsAsSql('profile', self::_getTableFieldsNames('profile'));
        $sql= str_replace('__PROFILE__', $profile, $sql);

        $result = new Result();
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


    // -----------------------------------------------------------------------------------------------------------------
    // Mandatory static methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function getDescription() {

        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
        $doc->setDescription('This request delete a user.')
            ->addEntityActionsRelationship(Entities::USER_PROFILE, Actions::SELECT)
            ->setType($doc::TYPE_SELECT)
            ->setSql(self::$__sql)
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