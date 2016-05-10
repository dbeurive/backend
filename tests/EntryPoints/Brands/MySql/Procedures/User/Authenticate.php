<?php

namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User;

use dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication;
use dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result;
use dbeurive\Backend\Database\Entrypoints\Description;
use dbeurive\Backend\Database\Connector\AbstractConnector;

use dbeurive\BackendTest\EntryPoints\Constants\Tags;
use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;

use dbeurive\BackendTest\EntryPoints\Constants\OutputValues;


/**
 * Class Authenticate
 * @package dbeurive\BackendTests\Database\Entrypoints\Procedure\User
 */

class Authenticate extends AbstractApplication {

    const SQL_AUTHENTICATE  = 'User/Authenticate';
    const KEY_AUTHENTICATED = 'authorized';

    static private $__mandatoryFields = [['user.login'], ['user.password']];

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
     */
    public function _init(array $inInitConfig=[]) {
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) {
        $outErrorMessage = null;
        if (false === $this->_checkMandatoryInputFields()) {
            $outErrorMessage = "Some mandatory fields are missing. Mandatory fields are: " . implode(', ', self::$__mandatoryFields) . "\nSee: " . __FILE__;
        }
        return true;
    }

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication
     */
    protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector) {
        $sql = $this->_getSql(self::SQL_AUTHENTICATE, [], $this->_getInputFields());
        $resultSql = $sql->execute();
        $result = new Result(Result::STATUS_SUCCESS,
            $resultSql->getDataSets(),
            [self::KEY_AUTHENTICATED => ! $resultSql->isDataSetsEmpty()]
        );
        return $result;
    }

    /**
     * Check if the user is authenticated.
     * @return bool If the user is authenticated, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    public function isAuthorized() {
        /** @var \dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result $result */
        $result = $this->getResult(); // So we have the auto completion.
        return $result->getValues()[self::KEY_AUTHENTICATED];
    }

    /**
     * If the user is authenticated, then the method returns the list of fields that describes the user.
     * @return array The method returns the list of fields that describes the user.
     */
    public function getUserData() {
        return $this->getResult()->getDataSets();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Mandatory static methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
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

