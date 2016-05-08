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

class Update extends AbstractApplication {

    private static $__conditionFields = ['user.id'];
    private static $__sql = "UPDATE user
                             SET __UPDATE__
                             WHERE  `user`.`id`=?";

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
        if (! UtilArray::array_keys_exists(self::$__conditionFields, $this->_executionConfig)) {
            $outErrorMessage = "Invalid SQL configuration. Mandatory fields are: " . implode(', ', self::$__conditionFields) . "\nSee: " . __FILE__;
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

        // Build the __UPDATE__ statement.
        $update = [];
        foreach ($this->_executionConfig as $_field => $_value) {
            if ('user.id' == $_field) {
                continue;
            }
            $update[] = $inLink->quoteFieldName($_field) . " = " . $inLink->quoteValue($_value);
        }

        $update = implode(',', $update);
        $sql = str_replace('__UPDATE__', $update, $this->_getSql());

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

    // -----------------------------------------------------------------------------------------------------------------
    // Mandatory static methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
        $doc->setDescription('This request upadates a user.')
            ->addEntityActionsRelationship(Entities::USER, Actions::UPDATE)
            ->setType($doc::TYPE_UPDATE)
            ->setSql(self::$__sql)
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