<?php

namespace dbeurive\BackendTest\Database\Doc;

use dbeurive\Backend\Database\Doc\Writer;
use dbeurive\BackendTest\EntryPoints\Constants\OutputValues;
use dbeurive\BackendTest\EntryPoints\Constants\Entities;
use dbeurive\BackendTest\EntryPoints\Constants\Tags;
use dbeurive\BackendTest\EntryPoints\Constants\Actions;
use dbeurive\Backend\Database\Doc\Option as DocOption;
use dbeurive\Backend\Database\SqlService\Option as SqlServiceOption;

// @runTestsInSeparateProcesses

class WriterTest extends \PHPUnit_Framework_TestCase
{
    use \dbeurive\BackendTest\SetUp;

    /**
     * @var \PDO Handler to the SQLite database.
     */
    private $__pdoSQLite;
    /**
     * @var string Path the the SQLite database.
     */
    private $__pathSQLite;

    public function setUp() {
        $this->__init();
        $this->__createMySqlPdo();
        $this->__createMySqlDatabase();
        // No link to the database is created.
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Data as it should be (the theory).
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var array Relations between fields and tables, as it should be.
     */
    private $__expectedLinkTablesFields = [
        'user' => [
            'user.id',
            'user.login',
            'user.password',
            'user.description'],
        'profile' => [
            'profile.id',
            'profile.fk_user_id',
            'profile.first_name',
            'profile.last_name']
    ];

    /**
     * @var array Fields used within conditions for each request, as it should be.
     */
    private $__expectedLinkRequestsConditionFields = [
        'Profile/Get' => [
            'profile.fk_user_id' ],
        'User/Authenticate' => [
            'user.login',
            'user.password' ],
        'User/Delete' => [
            'user.id' ],
        'User/Update' => [
            'user.id' ]
    ];

    /**
     * @var array Selected fields for each request, as it should be.
     */
    private $__expectedLinkRequestsSelectedFields = [
        'User/Authenticate' => [
            'user.id',
            'user.login',
            'user.password',
            'user.description'],
        'Profile/Get' => [
            'profile.id',
            'profile.fk_user_id',
            'profile.first_name',
            'profile.last_name'],
        'User/Select' => [
            'user.id',
            'user.login',
            'user.password',
            'user.description']
    ];

    /**
     * @var array Updated fields, as it should be.
     */
    private $__expectedLinkRequestsUpdatedFields = [
        'User/Update' => [
            'user.login',
            'user.password',
            'user.description']
    ];

    /**
     * @var array Inserted fields, as it should be.
     */
    private $__expectedLinkRequestsInsertedFields = [
        'User/Insert' => [
            'user.login',
            'user.password',
            'user.description']
    ];

    /**
     * @var array "Upserted" fields, as it should be.
     */
    private $__expectedLinkRequestsUpsertedFields = [
        'User/Upsert' => [
            'user.login',
            'user.password',
            'user.description']
    ];

    /**
     * @var array Fields used to organize the selected request's fields.
     */
    private $__expectedLinkRequestsRepresentationFields = [
        'Profile/Get' => [
            'profile.id']
    ];

    /**
     * @var array Tags for each request, as it should be.
     */
    private $__expectedLinkRequestsTags = [
        'User/Authenticate' => [
            Tags::AUTHENTICATION
        ]
    ];
    /**
     * @var array Configuration's parameters for each request, as it should be.
     */
    private $__expectedListingRequestParameters = [
        'User/Select' => [
            'limit_from',
            'limit_count']
    ];
    /**
     * @var array Output values for each request, as it should be.
     */
    private $__expectedListingRequestOutputValues  = [
        'User/Authenticate' => [
            OutputValues::OUTPUT_VALUE_IS_AUTHENTICATED
        ]
    ];
    /**
     * @var array (entity/action) relations for each request, as it should be.
     */
    private $__expectedLinkRequestsEntitiesActions = [
        'Profile/Get' => [
            ['entity' => Entities::USER_PROFILE, 'action' => Actions::SELECT ]
        ],
        'User/Authenticate' => [
            ['entity' => Entities::USER, 'action' => Actions::SELECT ]
        ],
        'User/Delete' => [
            ['entity' => Entities::USER, 'action' => Actions::DELETE ]
        ],
        'User/Insert' => [
            ['entity' => Entities::USER, 'action' => Actions::CREATE ]
        ],
        'User/Update' => [
            ['entity' => Entities::USER, 'action' => Actions::UPDATE ]
        ],
        'User/Upsert' => [
            ['entity' => Entities::USER, 'action' => Actions::UPSERT ]
        ],
        'User/Select' => [
            ['entity' => Entities::USER, 'action' => Actions::SELECT ]
        ]
    ];
    /**
     * @var array Relations between procedures and tags, as it should be.
     */
    private $__expectedLinkProceduresTags = [
        'User/Authenticate' => [
            Tags::AUTHENTICATION
        ],
        'User/Delete' => [
            Tags::ADMIN
        ]
    ];
    /**
     * @var array Relations between procedures and tags, as it should be.
     */
    private $__expectedLinkProceduresRequests = [
        'User/Authenticate' => [
            'User/Authenticate'
        ],
        'User/Delete' => [
            'User/Delete'
        ]
    ];
    /**
     * @var array Relations between procedures and input fields, as it should be.
     */
    private $__expectedLinkProceduresInputFields = [
        'User/Authenticate' => [
            'user.login',
            'user.password'
        ],
        'User/Delete' => [
            'user.id'
        ]
    ];
    /**
     * @var array Relations between procedures and output fields, as it should be.
     */
    private $__expectedLinkProceduresOutputFields = [
        'User/Authenticate' => [
            'user.id',
            'user.login',
            'user.password',
            'user.description'
        ]
    ];
    /**
     * @var array Relations between procedures and input parameters, as it should be.
     */
    private $__expectedListingProceduresInputParams = [
        'User/Delete' => [
            'suspend'
        ]
    ];
    /**
     * @var array Relations between procedures and output "data values", as it should be.
     */
    private $__expectedListingProceduresOutputDataValues = [
        'User/Authenticate' => [
            OutputValues::OUTPUT_VALUE_IS_AUTHENTICATED
        ]
    ];
    /**
     * @var array Relations between procedures and output values, as it should be.
     */
    private $__expectedListingProceduresOutputValues = [
        'User/Delete' => [
            OutputValues::OUTPUT_VALUE_IS_DELETED
        ]
    ];
    /**
     * @var array (entity/action) relations for each procedure, as it should be.
     */
    private $__expectedLinkProceduresEntitiesActions = [
        'User/Authenticate' => [
            ['entity' => Entities::USER, 'action' => Actions::AUTHENTICATE ]
        ],
        'User/Delete' => [
            ['entity' => Entities::USER, 'action' => Actions::DELETE ],
            ['entity' => Entities::USER_PROFILE, 'action' => Actions::DELETE ]
        ]
    ];

    public function testWriter() {

        // -------------------------------------------------------------------------------------------------------------
        // Generate the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        $config = $this->__generalConfiguration['application'];
        Writer::writer($config);

        // -------------------------------------------------------------------------------------------------------------
        // Open the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        $dbSqlitePath = $config[DocOption::DOC_PATH];
        $this->__pathSQLite = $dbSqlitePath;

        if (! file_exists($dbSqlitePath)) {
            throw new \Exception("Path to the SQLite database does not exist: $dbSqlitePath.");
        }

        print "Opening SQLite database:" . $dbSqlitePath;

        try {
            $this->__pdoSQLite = new \PDO("sqlite:${dbSqlitePath}");
            $this->__pdoSQLite->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->__pdoSQLite->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
        } catch (\Exception $e) {
            throw new \Exception("Can not open the SQLite database \"${dbSqlitePath}\" : " . $e->getMessage());
        }

        // -------------------------------------------------------------------------------------------------------------
        // Check all elements.
        // -------------------------------------------------------------------------------------------------------------

        $expectedFieldsList = [];
        $expectedTablesList = [];
        foreach ($this->__expectedLinkTablesFields as $_table => $_fields) {
            $expectedFieldsList = array_merge($_fields, $expectedFieldsList);
            $expectedTablesList[] = $_table;
        }

        $this->assertEquals(true, $this->__cmp($this->__getTables(),     $expectedTablesList));
        $this->assertEquals(true, $this->__cmp($this->__getFields(),     $expectedFieldsList));
        $this->assertEquals(true, $this->__cmp($this->__getTags(),       [Tags::AUTHENTICATION, Tags::ADMIN]));
        $this->assertEquals(true, $this->__cmp($this->__getEntities(),   [Entities::USER, Entities::USER_PROFILE]));
        $this->assertEquals(true, $this->__cmp($this->__getActions(),    [Actions::SELECT, Actions::DELETE, Actions::UPDATE, Actions::AUTHENTICATE, Actions::CREATE, Actions::UPSERT]));
        $this->assertEquals(true, $this->__cmp($this->__getRequests(),   ['User/Authenticate', 'User/Delete', 'User/Update', 'User/Insert', 'Profile/Get', 'User/Upsert', 'User/Select']));
        $this->assertEquals(true, $this->__cmp($this->__getProcedures(), ['User/Authenticate', 'User/Delete']));

        // -------------------------------------------------------------------------------------------------------------
        // Load the data from the SQLite database and compare it to the expected values.
        // -------------------------------------------------------------------------------------------------------------

        $linkTablesFields                 = $this->__getLinksTablesFields();
        $linkRequestsSelectedFields       = $this->__getLinksRequestsSelectedFields();
        $linkRequestUpdatedFields         = $this->__getLinksRequestsUpdatedFields();
        $linkRequestInsertedFields        = $this->__getLinksRequestsInsertedFields();
        $linkRequestUpsertedFields        = $this->__getLinksRequestsUpsertedFields();
        $linkRequestConditionFields       = $this->__getLinksRequestsConditionFields();
        $linkRequestParameters            = $this->__getLinksRequestsParameterValues();
        $linkRequestRepresentationFields  = $this->__getLinksRequestsRepresentationFields();
        $linkRequestTags                  = $this->__getLinksRequestsTags();
        $linksRequestsEntitiesActions     = $this->__getLinksRequestEntitiesActions();
        $listingRequestOutputValues       = $this->__getListingRequestsOutputValues();
        $linkProcedureTags                = $this->__getLinksProcedureTags();
        $linkProcedureRequests            = $this->__getLinksProcedureRequests();
        $linkProcedureEntitiesActions     = $this->__getLinksProcedureEntitiesActions();
        $linkProcedureInputFields         = $this->__getLinksProcedureInputFields();
        $linkProcedureOutputFields        = $this->__getLinksProcedureOutputFields();
        $listingProcedureInputParams      = $this->__getListingProcedureInputParams();
        $listingProcedureOutputDataValues = $this->__getListingProcedureOutputDataValues();
        $listingProcedureOutputValues     = $this->__getListingProcedureOutputValues();

        // -------------------------------------------------------------------------------------------------------------
        // Load data from the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        $flatter = function($v) { return [ $v['entity'], $v['action'] ]; };

        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkTablesFields), $linkTablesFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsSelectedFields), $linkRequestsSelectedFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsUpdatedFields), $linkRequestUpdatedFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsInsertedFields), $linkRequestInsertedFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsUpsertedFields), $linkRequestUpsertedFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsConditionFields), $linkRequestConditionFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedListingRequestParameters), $linkRequestParameters));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsRepresentationFields), $linkRequestRepresentationFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsTags), $linkRequestTags));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkRequestsEntitiesActions, $flatter), $linksRequestsEntitiesActions));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedListingRequestOutputValues), $listingRequestOutputValues));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkProceduresTags), $linkProcedureTags));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkProceduresRequests), $linkProcedureRequests));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkProceduresEntitiesActions, $flatter), $linkProcedureEntitiesActions));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkProceduresInputFields), $linkProcedureInputFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedLinkProceduresOutputFields), $linkProcedureOutputFields));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedListingProceduresInputParams), $listingProcedureInputParams));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedListingProceduresOutputDataValues), $listingProcedureOutputDataValues));
        $this->assertEquals(true, $this->__cmp($this->__expectedFlat($this->__expectedListingProceduresOutputValues), $listingProcedureOutputValues));
    }



    // -----------------------------------------------------------------------------------------------------------------
    // Entities.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Get the list of tables within the database.
     * @return array
     * @throws \Exception
     */
    private function __getTables() {
        $sql = 'SELECT "name" FROM "table"';
        $res = $this->__execSql($sql);
        return array_map(function($v) { return $v['name']; }, $res);
    }

    /**
     * Get the list of tags within the database.
     * @return array
     * @throws \Exception
     */
    private function __getTags() {
        $sql = 'SELECT "tag" FROM "tag"';
        $res = $this->__execSql($sql);
        return array_map(function($v) { return $v['tag']; }, $res);
    }

    /**
     * Get the list of entities within the database.
     * @return array
     * @throws \Exception
     */
    private function __getEntities() {
        $sql = 'SELECT "name" FROM "entity"';
        $res = $this->__execSql($sql);
        return array_map(function($v) { return $v['name']; }, $res);
    }

    /**
     * Get the list of actions within the database.
     * @return array
     * @throws \Exception
     */
    private function __getActions() {
        $sql = 'SELECT "name" FROM "action"';
        $res = $this->__execSql($sql);
        return array_map(function($v) { return $v['name']; }, $res);
    }

    /**
     * Get the list of requests within the database.
     * @return array
     * @throws \Exception
     */
    private function __getRequests() {
        $sql = 'SELECT "name" FROM "request"';
        $res = $this->__execSql($sql);
        return array_map(function($v) { return $v['name']; }, $res);
    }

    /**
     * Get the list of procedures within the database.
     * @return array
     * @throws \Exception
     */
    private function __getProcedures() {
        $sql = 'SELECT "name" FROM "procedure"';
        $res = $this->__execSql($sql);
        return array_map(function($v) { return $v['name']; }, $res);
    }

    /**
     * Get the list of fields within the database.
     * @return array
     * @throws \Exception
     */
    private function __getFields() {
        $sql = 'SELECT "name" FROM "field"';
        $res = $this->__execSql($sql);
        return array_map(function($v) { return $v['name']; }, $res);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Relations.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Load the links between tables and fields.
     * @return array
     * @throws \Exception
     */
    private function __getLinksTablesFields() {
        // SELECT "table"."name" as "table.name", "field"."name" as "field.name" FROM "field" INNER JOIN "table" ON "field"."table_id"="table"."id";
        $sql = 'SELECT "table"."name" as "table.name", "field"."name" as "field.name" FROM "field" INNER JOIN "table" ON "field"."table_id"="table"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['table.name'], $_value['field.name']];
        }
        return $ret;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Relations requests/*
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the links between the requests and their updated fields.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsUpdatedFields() {
        // SELECT "request"."name", "field"."name" FROM "requestUpdateField" INNER JOIN "request" ON "requestUpdateField"."request_id" = "request"."id" INNER JOIN "field" ON "requestUpdateField"."field_id" = "field"."id";
        $sql = 'SELECT "request"."name" as "request.name", "field"."name" as "field.name" FROM "requestUpdateField" ' .
            'INNER JOIN "request" ON "requestUpdateField"."request_id" = "request"."id" ' .
            'INNER JOIN "field" ON "requestUpdateField"."field_id" = "field"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the links between the requests and their inserted fields.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsInsertedFields() {
        // SELECT "request"."name", "field"."name" FROM "requestInsertField" INNER JOIN "request" ON "requestInsertField"."request_id" = "request"."id" INNER JOIN "field" ON "requestInsertField"."field_id" = "field"."id";
        $sql = 'SELECT "request"."name" as "request.name", "field"."name" as "field.name" FROM "requestInsertField" ' .
            'INNER JOIN "request" ON "requestInsertField"."request_id" = "request"."id" ' .
            'INNER JOIN "field" ON "requestInsertField"."field_id" = "field"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the links between the requests and their "upserted" fields.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsUpsertedFields() {
        // SELECT "request"."name", "field"."name" FROM "requestUpsertField" INNER JOIN "request" ON "requestUpsertField"."request_id" = "request"."id" INNER JOIN "field" ON "requestUpsertField"."field_id" = "field"."id";
        $sql = 'SELECT "request"."name" as "request.name", "field"."name" as "field.name" FROM "requestUpsertField" ' .
            'INNER JOIN "request" ON "requestUpsertField"."request_id" = "request"."id" ' .
            'INNER JOIN "field" ON "requestUpsertField"."field_id" = "field"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the links between the requests and tags.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsTags() {
        // SELECT "request"."name", "tag"."tag" FROM "requestTag" INNER JOIN "request" ON "requestTag"."request_id"="request"."id" INNER JOIN "tag" ON "requestTag"."tag_id"="tag"."id";
        $sql = 'SELECT "request"."name" as "request.name", "tag"."tag" as "tag.tag" FROM "requestTag" ' .
            'INNER JOIN "request" ON "requestTag"."request_id"="request"."id" ' .
            'INNER JOIN "tag" ON "requestTag"."tag_id"="tag"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['tag.tag']];
        }
        return $ret;
    }

    /**
     * Returns the links between the requests and the entities.
     * return array
     * @throws \Exception
     */
    private function __getLinksRequestEntitiesActions() {
        // SELECT "request"."name", "entity"."name", "action"."name" FROM "requestEntityAction" INNER JOIN "request" ON "requestEntityAction"."request_id"="request"."id" INNER JOIN "entity" ON "requestEntityAction"."entity_id"="entity"."id" INNER JOIN "action" ON "requestEntityAction"."action_id"="action"."id";
        $sql = 'SELECT "request"."name" as "request.name", "entity"."name" as "entity.name", "action"."name" as "action.name" FROM "requestEntityAction" ' .
            'INNER JOIN "request" ON "requestEntityAction"."request_id"="request"."id" ' .
            'INNER JOIN "entity" ON "requestEntityAction"."entity_id"="entity"."id" ' .
            'INNER JOIN "action" ON "requestEntityAction"."action_id"="action"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['entity.name'], $_value['action.name']];
        }
        return $ret;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Requests inputs and outputs.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the links between the requests and their selected fields.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsSelectedFields() {
        // SELECT "request"."name", "field"."name" FROM "requestSelectionField" INNER JOIN "request" ON "requestSelectionField"."request_id" = "request"."id" INNER JOIN "field" ON "requestSelectionField"."field_id" = "field"."id";
        $sql = 'SELECT "request"."name" as "request.name", "field"."name" as "field.name" FROM "requestSelectionField" ' .
            'INNER JOIN "request" ON "requestSelectionField"."request_id" = "request"."id" ' .
            'INNER JOIN "field" ON "requestSelectionField"."field_id" = "field"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the list of output values for each request.
     * @return array
     * @throws \Exception
     */
    private function __getListingRequestsOutputValues() {
        // SELECT "request"."name", "requestOutputDataValue"."name" FROM "requestOutputDataValue" INNER JOIN "request" ON "request"."id"="requestOutputDataValue"."request_id";
        $sql = 'SELECT "request"."name" as "request.name", "requestOutputDataValue"."name" as "requestOutputDataValue.name" FROM "requestOutputDataValue" INNER JOIN "request" ON "request"."id"="requestOutputDataValue"."request_id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['requestOutputDataValue.name']];
        }
        return $ret;
    }

    /**
     * Return the links between the requests and the fields used within conditions.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsConditionFields() {
        // SELECT "request"."name", "field"."name" FROM "requestConditionField" INNER JOIN "request" ON "requestConditionField"."request_id" = "request"."id" INNER JOIN "field" ON "requestConditionField"."field_id" = "field"."id";
        $sql = 'SELECT "request"."name" as "request.name", "field"."name" as "field.name" FROM "requestConditionField" ' .
            'INNER JOIN "request" ON "requestConditionField"."request_id" = "request"."id" ' .
            'INNER JOIN "field" ON "requestConditionField"."field_id" = "field"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the links between the requests and the fields used to organize the selected data.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsRepresentationFields() {
        // SELECT "request"."name", "field"."name" FROM "requestPresentationField" INNER JOIN "request" ON "requestPresentationField"."request_id" = "request"."id" INNER JOIN "field" ON "requestPresentationField"."field_id" = "field"."id";
        $sql = 'SELECT "request"."name" as "request.name", "field"."name" as "field.name" FROM "requestPresentationField" ' .
            'INNER JOIN "request" ON "requestPresentationField"."request_id" = "request"."id" ' .
            'INNER JOIN "field" ON "requestPresentationField"."field_id" = "field"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the links between the requests and the parameters used to configure them.
     * @return array
     * @throws \Exception
     */
    private function __getLinksRequestsParameterValues() {
        // SELECT "request"."name" as "request.name", "requestParameterValue"."name" as "requestParameterValue.name" FROM "requestParameterValue" INNER JOIN "request" ON "requestParameterValue"."request_id" = "request"."id";
        $sql = 'SELECT "request"."name" as "request.name", "requestParameterValue"."name" as "requestParameterValue.name" ' .
            'FROM "requestParameterValue" INNER JOIN "request" ON "requestParameterValue"."request_id" = "request"."id";';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['request.name'], $_value['requestParameterValue.name']];
        }
        return $ret;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Relations procedures
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Returns the links between the procedures and the entities.
     * return array
     * @throws \Exception
     */
    private function __getLinksProcedureEntitiesActions() {
        // SELECT "procedure"."name", "entity"."name", "action"."name" FROM "procedureEntityAction" INNER JOIN "procedure" ON "procedureEntityAction"."procedure_id"="procedure"."id" INNER JOIN "entity" ON "procedureEntityAction"."entity_id"="entity"."id" INNER JOIN "action" ON "procedureEntityAction"."action_id"="action"."id";
        $sql = 'SELECT "procedure"."name" as "procedure.name", "entity"."name" as "entity.name", "action"."name" as "action.name" FROM "procedureEntityAction" ' .
            'INNER JOIN "procedure" ON "procedureEntityAction"."procedure_id"="procedure"."id" ' .
            'INNER JOIN "entity" ON "procedureEntityAction"."entity_id"="entity"."id" ' .
            'INNER JOIN "action" ON "procedureEntityAction"."action_id"="action"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['entity.name'], $_value['action.name']];
        }
        return $ret;
    }

    /**
     * Return the links between procedures and tags.
     * @return array
     * @throws \Exception
     */
    private function __getLinksProcedureTags() {
        // SELECT "procedure"."name", "tag"."tag" FROM "procedureTag" INNER JOIN "procedure" ON "procedureTag"."procedure_id"="procedure"."id" INNER JOIN "tag" ON "procedureTag"."tag_id"="tag"."id";
        $sql = 'SELECT "procedure"."name" as "procedure.name", "tag"."tag" as "tag.tag" FROM "procedureTag" ' .
            'INNER JOIN "procedure" ON "procedureTag"."procedure_id"="procedure"."id" ' .
            'INNER JOIN "tag" ON "procedureTag"."tag_id"="tag"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['tag.tag']];
        }
        return $ret;
    }

    /**
     * Return the links between procedures and requests.
     * @return array
     * @throws \Exception
     */
    private function __getLinksProcedureRequests() {
        // SELECT "procedure"."name", "request"."name" FROM "procedureRequest" INNER JOIN "procedure" ON "procedure"."id"="procedureRequest"."procedure_id" INNER JOIN "request" ON "procedureRequest"."request_id"="request"."id";
        $sql = 'SELECT "procedure"."name" as  "procedure.name", "request"."name" as "request.name" FROM "procedureRequest" ' .
            'INNER JOIN "procedure" ON "procedure"."id"="procedureRequest"."procedure_id" ' .
            'INNER JOIN "request" ON "procedureRequest"."request_id"="request"."id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['request.name']];
        }
        return $ret;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Procedure inputs
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the links between procedures and input fields.
     * @return array
     * @throws \Exception
     */
    private function __getLinksProcedureInputFields() {
        // SELECT "procedure"."name", "field"."name" FROM "procedureInputField" INNER JOIN "procedure" ON "procedure"."id"="procedureInputField"."procedure_id" INNER JOIN "field" ON "field"."id"="procedureInputField"."field_id";
        $sql = 'SELECT "procedure"."name" as "procedure.name", "field"."name" as  "field.name" FROM "procedureInputField" ' .
            'INNER JOIN "procedure" ON "procedure"."id"="procedureInputField"."procedure_id" ' .
            'INNER JOIN "field" ON "field"."id"="procedureInputField"."field_id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the links between procedures and input parameters.
     * @return array
     * @throws \Exception
     */
    private function __getListingProcedureInputParams() {
        // SELECT "procedure"."name", "procedureInputParam"."name" FROM "procedureInputParam" INNER JOIN "procedure" ON "procedure"."id"="procedureInputParam"."procedure_id";
        $sql = 'SELECT "procedure"."name" as "procedure.name", "procedureInputParam"."name" as "procedureInputParam.name" FROM "procedureInputParam" INNER JOIN "procedure" ON "procedure"."id"="procedureInputParam"."procedure_id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['procedureInputParam.name']];
        }
        return $ret;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Procedure output
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the links between procedures and output fields.
     * @return array
     * @throws \Exception
     */
    private function __getLinksProcedureOutputFields() {
        // SELECT "procedure"."name", "field"."name" FROM "procedureOutputField" INNER JOIN "procedure" ON "procedure"."id"="procedureOutputField"."procedure_id" INNER JOIN "field" ON "field"."id"="procedureOutputField"."field_id";
        $sql = 'SELECT "procedure"."name" as "procedure.name", "field"."name" as "field.name" FROM "procedureOutputField" ' .
            'INNER JOIN "procedure" ON "procedure"."id"="procedureOutputField"."procedure_id" ' .
            'INNER JOIN "field" ON "field"."id"="procedureOutputField"."field_id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['field.name']];
        }
        return $ret;
    }

    /**
     * Return the links between procedures and output "data values".
     * @return array
     * @throws \Exception
     */
    private function __getListingProcedureOutputDataValues() {
        // SELECT "procedure"."name", "procedureOutputDataValue"."name" FROM "procedureOutputDataValue" INNER JOIN "procedure" ON "procedure"."id"="procedureOutputDataValue"."procedure_id";
        $sql = 'SELECT "procedure"."name" as "procedure.name", "procedureOutputDataValue"."name" as  "procedureOutputDataValue.name" FROM "procedureOutputDataValue" ' .
            'INNER JOIN "procedure" ON "procedure"."id"="procedureOutputDataValue"."procedure_id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['procedureOutputDataValue.name']];
        }
        return $ret;
    }

    /**
     * Return the links between procedures and output values.
     * @return array
     * @throws \Exception
     */
    private function __getListingProcedureOutputValues() {
        // SELECT "procedure"."name", "procedureOutputValue"."name" FROM "procedureOutputValue" INNER JOIN "procedure" ON "procedure"."id"="procedureOutputValue"."procedure_id";
        $sql = 'SELECT "procedure"."name" as "procedure.name", "procedureOutputValue"."name" as  "procedureOutputValue.name" FROM "procedureOutputValue" ' .
            'INNER JOIN "procedure" ON "procedure"."id"="procedureOutputValue"."procedure_id"';
        $res = $this->__execSql($sql);
        $ret = [];
        foreach ($res as $_index => $_value) {
            $ret[] = [$_value['procedure.name'], $_value['procedureOutputValue.name']];
        }
        return $ret;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Utilities.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Execute a SELECT request and return the selection.
     * @param string $inSql SQL request to execute.
     * @return array The method returns the selection.
     * @throws \Exception
     */
    private function __execSql($inSql, $inFetchStyle=\PDO::FETCH_ASSOC) {
        $req = $this->__pdoSQLite->prepare($inSql);
        if (false === $req->execute([])) {
            throw new \Exception("Error on $inSql. Database: " . $this->__pathSQLite);
        }
        return $req->fetchAll($inFetchStyle);
    }

    /**
     * Compare a given "theoretical" result to a given "factual" result.
     * @param array $inTheory Theoretical result.
     * @param array $inFact Factual result.
     * @return bool If the two results are identical, then the method returns the value true.
     * Otherwise, the method returns the value false.
     */
    private function __cmp(array $inTheory, array $inFact) {
        sort($inTheory);
        sort($inFact);

        if (count($inFact) != count($inFact)) {
            return false;
        }

        if (json_encode($inTheory) != json_encode($inFact)) {
            return false;
        }

        return true;
    }

    /**
     * This method converts a structure that represents an expected result into a flat structure (as returned by a selection to the database).
     * @param array $inExpected Structure that represents the an expected result.
     * @maram callable\null $inOptFlatter Function used to flat one element of the expected structure.
     * @return array
     */
    private function __expectedFlat(array $inExpected, $inOptFlatter=null) {
        $res = [];
        foreach ($inExpected as $_k => $_v) {
            foreach ($_v as $_index => $__v) {

                $tail = null;
                if (is_null($inOptFlatter)) {
                    $tail = is_array($__v) ? $__v : [$__v];
                } else {
                    $tail = call_user_func($inOptFlatter, $__v);
                }
                $res[] = array_merge([$_k], $tail);
            }
        }
        return $res;
    }

}