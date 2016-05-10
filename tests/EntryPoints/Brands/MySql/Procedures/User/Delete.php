<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User;

use dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication;
use dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Connector\AbstractConnector;

use dbeurive\BackendTest\EntryPoints\Constants\OutputValues;
use dbeurive\BackendTest\EntryPoints\Constants\Tags;
use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;


class Delete extends AbstractApplication {

    const SQL_DELETE    = 'User/Delete';
    const PARAM_SUSPEND = 'suspend';
    static private $__mandatoryFields = [['user.id']];

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function _init(array $inInitConfig=[]) {
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) {
        return true;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector) {
        $sql = $this->_getSql(self::SQL_DELETE, [], $this->_getInputFields());
        $resultSql = $sql->execute();
        $result = new Result(Result::STATUS_SUCCESS,
            $resultSql->getDataSets() // Should be empty, since it is a DELETE.
        );
        return $result;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function getDescription() {
        $doc = new Description\Procedure();
        $doc->setDescription("This procedure is used to delete a user.")
            ->setRequests([self::SQL_DELETE])
            ->setMandatoryInputFields(self::$__mandatoryFields)
            ->addTags(Tags::ADMIN)
            ->addOutputValue(OutputValues::OUTPUT_VALUE_IS_DELETED)
            ->addEntityActionsRelationship(Entities::USER, Actions::DELETE)
            ->addEntityActionsRelationship(Entities::USER_PROFILE, Actions::DELETE)
            ->addMandatoryInputParam('suspend');

            // Note that the following methods are not called:
            // - addRequest
            // - setMandatoryInputFields
            // - setOptionalInputFields
            // - setMandatoryInputParams
            // - setOptionalInputParams
            // - setOutputFields
            // - addEntityActionsRelationship
            // - addMandatoryInputParam
            // - addOptionalInputParam
            // - addOutputDataValue
            // - setOutputIsMulti

        return $doc;
    }
}