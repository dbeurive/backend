<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User;

use dbeurive\Backend\Database\EntryPoints\AbstractProcedure;
use dbeurive\BackendTest\EntryPoints\Result\ProcedureResult;
use dbeurive\Backend\Database\EntryPoints\Description;


use dbeurive\BackendTest\EntryPoints\Constants\Tags;
use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;
use dbeurive\BackendTest\EntryPoints\Constants\OutputValues;


/**
 * Class Authenticate
 * @package dbeurive\BackendTest\Database\EntryPoints\Procedure\User
 */

class Authenticate extends AbstractProcedure {

    const SQL_AUTHENTICATE  = 'User/Authenticate';
    const KEY_AUTHENTICATED = 'authorized';
    static private $__mandatoryFields = [['user.login'], ['user.password']];

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function execute($inExecutionConfig) {
        $sql = $this->getSql(self::SQL_AUTHENTICATE);
        $resultSql = $sql->execute($inExecutionConfig);
        $result = new ProcedureResult(ProcedureResult::STATUS_SUCCESS,
            $resultSql->getDataSets(),
            [self::KEY_AUTHENTICATED => ! $resultSql->isDataSetsEmpty()]
        );
        return $result;
    }

    /**
     * @see \dbeurive\Backend\Database\EntryPoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new Description\Procedure();
        $doc->setDescription("This procedure is used to authenticate a user based on a provided set of login and password.")
            ->setRequests([self::SQL_AUTHENTICATE])
            ->addTags(Tags::AUTHENTICATION)
            ->setMandatoryInputFields(self::$__mandatoryFields)
            ->addOutputField('user.*')
            ->addOutputDataValue(OutputValues::OUTPUT_VALUE_IS_AUTHENTICATED, 'This flag indicates whether the user has been successfully authenticated or not. TRUE: authentication succeed, FALSE: authentication failed.')
            ->addEntityActionsRelationship(Entities::USER, Actions::AUTHENTICATE);

        // Note that the following methods are not called:
        // - addRequest
        // - addMandatoryInputField
        // - addOptionalInputField
        // - setOptionalInputFields
        // - addMandatoryInputParam
        // - setMandatoryInputParams
        // - addOptionalInputParam
        // - setOptionalInputParams
        // - setOutputFields
        // - setOutputIsMulti

        return $doc;
    }
}

