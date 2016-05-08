<?php

/**
 * This file implements the process of extracting and organising the information from all the API's entry points.
 * This process is invoked within the execution of all "documentation writers".
 *
 * "Documentation writers" performs the following actions:
 *     1. Extract information from the database.
 *     2. Extract information from all the API's entry points.
 *     3. Organize the information previously extracted.
 */

namespace dbeurive\Backend\Database\Doc;


use dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription;
use dbeurive\Backend\Database\Link\AbstractLink;
use dbeurive\Backend\Database\Link\Option as LinkOption;
use dbeurive\Backend\Database\Entrypoints\Description\Element\Field;
use dbeurive\Backend\Database\Entrypoints\Description\Element\Table;
use dbeurive\Backend\Database\Entrypoints\Option as EntryPointOption;
use dbeurive\Backend\Database\Doc\Option as DocOption;
use dbeurive\Backend\Database\Entrypoints\Description\Procedure as ProcedureDescription;
use dbeurive\Backend\Database\Entrypoints\Description\Sql as SqlDescription;
use dbeurive\Util\UtilData;
use dbeurive\Backend\Database\Entrypoints\Description\Element\Tag as Tag;
use dbeurive\Backend\Database\DatabaseInterface;
use dbeurive\Backend\Cli\Lib\CliWriter;
use dbeurive\Input\SpecificationsSet;
use dbeurive\Input\Specification;


/**
 * Class Writer
 * @package dbeurive\Backend\Database\Doc
 */

class Writer {

    /**
     * This method checks a given configuration.
     * @param array $inConfiguration List of parameters that define the configuration.
     * @return array If the given configuration is valid, then the method returns an empty array.
     *         Otherwise, the method returns a list of error messages.
     */
    static public function checkConfiguration(array $inConfiguration) {
        $set = new SpecificationsSet();
        $set->addInputSpecification(new Specification(DocOption::DOC_DB_REPO_PATH))
            ->addInputSpecification(new Specification(DocOption::DOC_DB_FILE_BASENAME))
            ->addInputSpecification(new Specification(DocOption::PHP_DB_DESC_PATH))
            ->addInputSpecification(new Specification(EntryPointOption::PROC_BASE_NS))
            ->addInputSpecification(new Specification(EntryPointOption::SQL_BASE_NS))
            ->addInputSpecification(new Specification(EntryPointOption::PROC_REPO_PATH))
            ->addInputSpecification(new Specification(EntryPointOption::SQL_REPO_PATH))
            ->addInputSpecification(new Specification(LinkOption::LINK_NAME))
            ->addInputSpecification(new Specification(LinkOption::LINK_CONFIG));

        if ($set->check($inConfiguration)) {
            return [];
        }

        return array_values($set->getErrorsOnInputsInIsolationFromTheOthers());
    }

    /**
     * Given the raw representation of the database, this method returns the corresponding "high-level" representation.
     * @param array $inRawSchema Schema of the database, as returned by the method `\dbeurive\Backend\Database\Link\AbstractLink::getDatabaseSchema()`.
     *              This is an associative array:
     *              array(   <table name> => array(<field name>, <field name>...),
     *                       <table name> => array(<field name>, <field name>...),
     *                       ...)
     * @return array The method returns the "high-level" representation of the database.
     *         This is an associative array:
     *         array(   'fields' => <list of instances of \dbeurive\Backend\Database\Entrypoints\Description\Element\Field>,
     *                  'tables' => <list of instances of \dbeurive\Backend\Database\Entrypoints\Description\Element\Table>
     *              )
     * @throws \Exception
     */
    private static function __buildDatabaseSchema(array $inRawSchema) {
        $fields = array(); // Array of \dbeurive\Backend\Database\Entrypoints\Description\Element\Field
        $tables = array(); // Array of \dbeurive\Backend\Database\Entrypoints\Description\Element\Table

        foreach ($inRawSchema as $_tableName => $_fields) {
            if (false === Table::getByClassAndName(Table::getFullyQualifiedClassName(), $_tableName)) {
                $table = new Table($_tableName);
                $table->addToRepository();
                $tables[] = new Table($_tableName);
            }

            foreach ($_fields as $field) {
                $fields[] = new Field(implode('.', array($_tableName, $field)));
            }
        }

        return array('fields' => $fields, 'tables' => $tables);
    }

    /**
     * Create a SQLite database that contains the information extracted from all the API's entry points.
     * @param array $inConfiguration Configuration.
     *        This array must contain the following entries;
     *           * \dbeurive\Backend\Database\Doc\Option::DOC_DB_REPO_PATH
     *           * \dbeurive\Backend\Database\Doc\Option::DOC_DB_FILE_BASENAME
     *           * \dbeurive\Backend\Database\Doc\Option::PHP_DB_DESC_PATH
     *           * \dbeurive\Backend\Database\Entrypoints\Option::SQL_BASE_NS
     *           * \dbeurive\Backend\Database\Entrypoints\Option::PROC_BASE_NS
     *           * \dbeurive\Backend\Database\Entrypoints\Option::SQL_REPO_PATH
     *           * \dbeurive\Backend\Database\Entrypoints\Option::PROC_REPO_PATH
     *           * \dbeurive\Backend\Database\Link\Option::LINK_NAME
     *           * \dbeurive\Backend\Database\Link\Option::LINK_CONFIG
     * @return bool Upon successful completion the method returns the value true.
     *         Otherwise an exception is thrown.
     * @throws \Exception
     */
    static public function writer(array $inConfiguration)
    {
        AbstractDescription::reset();

        // -------------------------------------------------------------------------------------------------------------
        // Extract data from the configuration.
        // -------------------------------------------------------------------------------------------------------------

        $docRepositoryDir        = $inConfiguration[DocOption::DOC_DB_REPO_PATH];
        $docRepositoryName       = $inConfiguration[DocOption::DOC_DB_FILE_BASENAME];
        $phpDbDescription        = $inConfiguration[DocOption::PHP_DB_DESC_PATH];
        $sqlBaseNamespace        = $inConfiguration[EntryPointOption::SQL_BASE_NS];
        $procedureBaseNamespace  = $inConfiguration[EntryPointOption::PROC_BASE_NS];
        $sqlRepositoryPath       = $inConfiguration[EntryPointOption::SQL_REPO_PATH];
        $procedureRepositoryPath = $inConfiguration[EntryPointOption::PROC_REPO_PATH];
        $linkClassName           = $inConfiguration[LinkOption::LINK_NAME];
        $linkConfig              = $inConfiguration[LinkOption::LINK_CONFIG];

        $docBaseName = $docRepositoryDir . DIRECTORY_SEPARATOR . $docRepositoryName;
        $sqliteSchemaPath = __DIR__ . DIRECTORY_SEPARATOR . 'schema.php';

        // -------------------------------------------------------------------------------------------------------------
        // Initialize the database adaptor and execute it in order to get the database' schema.
        // -------------------------------------------------------------------------------------------------------------

        /** @var AbstractLink $link */
        $link = new $linkClassName();
        $link->setConfiguration($linkConfig);
        $link->connect();
        $dataInterface = DatabaseInterface::getInstance();
        $dataInterface->setDbLink($link);

        CliWriter::echoInfo("Extracting data from the __REAL__ database.");
        CliWriter::echoInfo('   Get the list of all tables and fields in the database.');

        /* @var array $rawDatabaseSchema */
        $rawDatabaseSchema = $link->getDatabaseSchema();
        if (false === $rawDatabaseSchema) {
            throw new \Exception("Error while extracting the schema of the database! " . $link->getErrorMessage());
        }

        /* @var array $databaseSchema */
        $databaseSchema = self::__buildDatabaseSchema($rawDatabaseSchema);

        /* @var array $allFields */
        $allFields = $databaseSchema['fields'];  // List of \dbeurive\Backend\Database\Entrypoints\Description\Element\Field
        /* @var array $allTables */
        $allTables = $databaseSchema['tables'];  // List of \dbeurive\Backend\Database\Entrypoints\Description\Element\Table

        // -------------------------------------------------------------------------------------------------------------
        // Configure the database service's provider, then execute it.
        // -------------------------------------------------------------------------------------------------------------

        $dataInterface->setSqlRepositoryBasePath($sqlRepositoryPath);
        $dataInterface->setProcedureRepositoryBasePath($procedureRepositoryPath);
        $dataInterface->setSqlBaseNameSpace($sqlBaseNamespace);
        $dataInterface->setProcedureBaseNameSpace($procedureBaseNamespace);
        $dataInterface->setPhpDatabaseRepresentationPath($phpDbDescription);
        $dataInterface->setDatabaseSchema($rawDatabaseSchema);

        CliWriter::echoInfo("Extracting data from PHP codes (SQL requests and procedures).");

        $sqlDescriptions = $dataInterface->getAllSqlDescriptions(); // List of SqlDescription
        $procedureDescriptions = $dataInterface->getAllProceduresDescriptions(); // List of ProcedureDescription

        // -------------------------------------------------------------------------------------------------------------
        // Check and expend all fields' names within the documentation.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Check all fields within the SQL requests and the procedures descriptions.");
        \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::setDbFields_($allFields);
        $error = null;
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription $_description */
        foreach (array_merge($sqlDescriptions, $procedureDescriptions) as $_description) {
            if (false === $_description->check($error)) {
                CliWriter::echoError($error);
                exit(1);
            }
        }

        CliWriter::echoInfo("Everything looks good. Create data repositories.");

        // -------------------------------------------------------------------------------------------------------------
        // Delete the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        $dbSqlite = "${docBaseName}.sqlite";
        CliWriter::echoInfo("   Delete the SQLite database \"${dbSqlite}\".");
        if (file_exists($dbSqlite)) {
            if (!unlink($dbSqlite)) {
                CliWriter::echoError("Can not delete file ${dbSqlite}.");
                exit(1);
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Delete the JSON database.
        // -------------------------------------------------------------------------------------------------------------

        $dbJson = "${docBaseName}.json";
        CliWriter::echoInfo("   Delete the JSON file \"${dbJson}\" representation.");
        if (file_exists($dbJson)) {
            if (!unlink($dbJson)) {
                CliWriter::echoError("Can not delete file ${dbJson}.");
                exit(1);
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Delete the PHP file that represents the database' schema.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Delete the PHP file \"${phpDbDescription}\" the represents the database' schema.");
        if (file_exists($phpDbDescription)) {
            if (!unlink($phpDbDescription)) {
                CliWriter::echoError("Can not delete file ${phpDbDescription}.");
                exit(1);
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Open SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        $pdo = null;
        CliWriter::echoInfo("   Open the SQLite database \"${dbSqlite}\"");
        try {
            $pdo = new \PDO("sqlite:${dbSqlite}");
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
        } catch (\Exception $e) {
            CliWriter::echoError("Can not open the SQLite database \"${dbSqlite}\" : " . $e->getMessage());
            exit(1);
        }

        // EntryPointProvider::setDatabaseHandler($pdo); // Not used... but called for pedagogic purposes.

        // -------------------------------------------------------------------------------------------------------------
        // Create the database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Create the SQLite database \"${dbSqlite}\".");
        $schema = require $sqliteSchemaPath;
        foreach ($schema as $_sql) {
            if (false === $pdo->query($_sql)) {
                CliWriter::echoError("Can not execute SQLite request $_sql.");
                exit(1);
            }
        }

        CliWriter::echoInfo("Inject data extracted from the database and from the PHP code into the SQLite database.");

        // -------------------------------------------------------------------------------------------------------------
        // Save the list of all tables to the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Save the list of all tables into the SQLite database.");
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Element\Table $_table */
        foreach ($allTables as $_table) {
            $sql = "INSERT INTO 'table' (name) VALUES (:name)";
            $req = $pdo->prepare($sql);
            if (false === $req->execute(['name' => $_table->getName()])) {
                CliWriter::echoError("Can not insert table's name \"${_table}\" into SQLite database \"${dbSqlite}\".");
                exit(1);
            }
            $_table->setId($pdo->lastInsertId());
        }

        // -------------------------------------------------------------------------------------------------------------
        // Save the list of all entities to the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Save the list of all entities into the SQLite database.");
        foreach (\dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::getAllIdentifiedEntities_() as $_entityName) {
            $entity = new \dbeurive\Backend\Database\Entrypoints\Description\Element\Entity($_entityName);
            $sql = "INSERT INTO 'entity' (name) VALUES (:name)";
            $req = $pdo->prepare($sql);
            if (false === $req->execute(['name' => $entity->getName()])) {
                CliWriter::echoError("Can not insert entity's name \"${_entityName}\" into SQLite database \"${dbSqlite}\".");
                exit(1);
            }
            $entity->setId($pdo->lastInsertId());
        }


        // -------------------------------------------------------------------------------------------------------------
        // Save the list of all actions to the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Save the list of all actions into the SQLite database.");
        foreach (\dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::getAllIdentifiedActions_() as $_actionName) {
            $action = new \dbeurive\Backend\Database\Entrypoints\Description\Element\Action($_actionName);
            $sql = "INSERT INTO 'action' (name) VALUES (:name)";
            $req = $pdo->prepare($sql);
            if (false === $req->execute(['name' => $action->getName()])) {
                CliWriter::echoError("Can not insert action's name \"${_actionName}\" into SQLite database \"${dbSqlite}\".");
                exit(1);
            }
            $action->setId($pdo->lastInsertId());
        }

        // -------------------------------------------------------------------------------------------------------------
        // Save the list of all the fields for all the tables to the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Save the list of all tables' fields into the SQLite database.");
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Element\Field $_field */
        foreach ($allFields as $_index => $_field) {
            $tableId = $_field->getTable()->getId();
            $fieldName = $_field->getName();
            $sql = "INSERT INTO field (table_id, name) VALUES (:table_id, :name)";
            $req = $pdo->prepare($sql);
            if (false === $req->execute(['table_id' => $tableId,
                    'name' => $fieldName])
            ) {
                CliWriter::echoError("Can not insert table's field name \"${fieldName}\" into SQLite database ${dbSqlite}.");
                exit(1);
            }
            $_field->setId($pdo->lastInsertId());
        }

        // -------------------------------------------------------------------------------------------------------------
        // Save the list of all tags.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Save the list of all tags into the SQLite database.");
        $apiEntryPointDescriptions = array_merge($procedureDescriptions, $sqlDescriptions);
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription $_description */
        foreach ($apiEntryPointDescriptions as $_description) {
            /* @var string $_tag */
            foreach ($_description->getTags_() as $_tag) {

                if (false !== Tag::getByClassAndName(Tag::getFullyQualifiedClassName(), $_tag)) {
                    continue;
                }

                $tag = new Tag($_tag);
                $sql = "INSERT INTO tag (tag) VALUES (:tag)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['tag' => $tag->getName()])) {
                    CliWriter::echoError("Can not insert SQL request tag \"{$tag->getName()}\" into SQLite database ${dbSqlite}.");
                    exit(1);
                }

                $tag->setId($pdo->lastInsertId());
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Save the list of all SQL descriptions to the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Save the list of all SQL requests into the SQLite database.");
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Sql $_description */
        foreach ($sqlDescriptions as $_description) {
            $multiSql = 0;
            $sqlReq = $_description->getSql_();

            if (is_array($_description->getSql_())) {
                $sqlReq = json_encode($_description->getSql_());
                $multiSql = 1;
            }

            $sql = "INSERT INTO request (sql, multiSql, description, name, type) VALUES (:sql, :multiSql, :description, :name, :type)";
            $req = $pdo->prepare($sql);
            if (false === $req->execute(['sql' => $sqlReq,
                    'multiSql' => $multiSql,
                    'description' => $_description->getDescription_(),
                    'name' => $_description->getName_(),
                    'type' => $_description->getType_()])
            ) {
                CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                exit(1);
            }

            $_description->setId_($pdo->lastInsertId());
            $_description->addToRepository_();
        }

        // -------------------------------------------------------------------------------------------------------------
        // Save the list of all procedures' descriptions to the SQLite database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Save the list of all procedures into the SQLite database.");
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Procedure $_description */
        foreach ($procedureDescriptions as $_description) {
            $sql = "INSERT INTO procedure (name, description, resultMultiRow) VALUES (:name, :description, :multi)";
            $req = $pdo->prepare($sql);
            if (false === $req->execute(['description' => $_description->getDescription_(),
                    'name' => $_description->getName_(),
                    'multi' => $_description->isOutputMulti_() ? 1 : 0])
            ) {
                CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                exit(1);
            }

            $_description->setId_($pdo->lastInsertId());
            $_description->addToRepository_();
        }

        CliWriter::echoInfo("Build relations between the data extracted from the database and the data extracted from the PHP code.");

        // -------------------------------------------------------------------------------------------------------------
        // Build the relations between requests, tags, entities, parameters and fields.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Build the relations between SQL requests, tags, output values, fields, parameters and entities.");
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Sql $_description */
        foreach ($sqlDescriptions as $_description) {

            // echo "\t\to " . $_description->getName_() . "\n";

            /* @var string $_field */
            foreach ($_description->getSelectedFields_() as $_field) {
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $_field);
                if (false === $field) {
                    CliWriter::echoError("Error: could not find the (selected) field '$_field' in the database's documentation. Is your database up to date ?");
                    exit(1);
                }
                $sql = "INSERT INTO requestSelectionField (request_id, field_id) VALUES (:request_id, :field_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(), 'field_id' => $field->getId()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var string $_field */
            foreach ($_description->getUpdatedFields_() as $_field) {
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $_field);
                if (false === $field) {
                    CliWriter::echoError("Error: could not find the (updated) field '$_field' in the database's documentation. Is your database up to date ?");
                    exit(1);
                }
                $sql = "INSERT INTO requestUpdateField (request_id, field_id) VALUES (:request_id, :field_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(), 'field_id' => $field->getId()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var string $_field */
            foreach ($_description->getInsertedFields_() as $_field) {
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $_field);
                if (false === $field) {
                    CliWriter::echoError("Error: could not find the (inserted) field '$_field' in the database's documentation. Is your database up to date ?");
                    exit(1);
                }
                $sql = "INSERT INTO requestInsertField (request_id, field_id) VALUES (:request_id, :field_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(), 'field_id' => $field->getId()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var string $_field */
            foreach ($_description->getUpsertedFields_() as $_field) {
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $_field);
                if (false === $field) {
                    CliWriter::echoError("Error: could not find the (upserted) field '$_field' in the database's documentation. Is your database up to date ?");
                    exit(1);
                }
                $sql = "INSERT INTO requestUpsertField (request_id, field_id) VALUES (:request_id, :field_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(), 'field_id' => $field->getId()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var string $_field */
            foreach ($_description->getConditionFields_() as $_field) {
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $_field);
                if (false === $field) {
                    CliWriter::echoError("Error: could not find the field (used within the WHERE clause) '$_field' in the database's documentation. Is your database up to date ?");
                    exit(1);
                }
                $sql = "INSERT INTO requestConditionField (request_id, field_id) VALUES (:request_id, :field_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(), 'field_id' => $field->getId()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var string $_field */
            foreach ($_description->getPresentationFields_() as $_field) {
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $_field);
                if (false === $field) {
                    CliWriter::echoError("Error: could not find the field '$_field' in the database's documentation. Is your database up to date ?");
                    exit(1);
                }
                $sql = "INSERT INTO requestPresentationField (request_id, field_id) VALUES (:request_id, :field_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(), 'field_id' => $field->getId()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var string $_tag */
            foreach ($_description->getTags_() as $_tag) {
                $tag = Tag::getByClassAndName(Tag::getFullyQualifiedClassName(), $_tag);
                $sql = "INSERT INTO requestTag (tag_id, request_id) VALUES (:tag_id, :request_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['tag_id' => $tag->getId(), 'request_id' => $_description->getId_()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_value */
            foreach ($_description->getOutputDataValues_() as $_value) {
                $name = $_value[\dbeurive\Backend\Database\Entrypoints\Description\Sql::KEY_NAME];
                $apiEntryPointDescriptions = $_value[\dbeurive\Backend\Database\Entrypoints\Description\Sql::KEY_DESCRIPTION];
                $sql = "INSERT INTO requestOutputDataValue (request_id, name, description) VALUES (:request_id, :name, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(), 'name' => $name, 'description' => $apiEntryPointDescriptions])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_param */
            foreach ($_description->getParameterValues_() as $_param) {
                $name = $_param[SqlDescription::KEY_NAME];
                $description = $_param[SqlDescription::KEY_DESCRIPTION];
                $sql = "INSERT INTO requestParameterValue (request_id, name, description) VALUES (:request_id, :name, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['request_id' => $_description->getId_(),
                        'name' => $name,
                        'description' => is_null($description) ? '' : $description])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_actionsList */
            foreach ($_description->getEntitiesActions_() as $_entityName => $_actionsList) {
                $entity = \dbeurive\Backend\Database\Entrypoints\Description\Element\Entity::getByClassAndName(\dbeurive\Backend\Database\Entrypoints\Description\Element\Entity::getFullyQualifiedClassName(), $_entityName);
                if (false === $entity) {
                    CliWriter::echoError("The entity named ${_entityName} is not found");
                    exit(1);
                }

                foreach ($_actionsList as $_actionName) {
                    $action = \dbeurive\Backend\Database\Entrypoints\Description\Element\Action::getByClassAndName(\dbeurive\Backend\Database\Entrypoints\Description\Element\Action::getFullyQualifiedClassName(), $_actionName);
                    if (false === $action) {
                        CliWriter::echoError("The action named ${_actionName} is not found");
                        exit(1);
                    }

                    $sql = "INSERT INTO requestEntityAction (request_id, entity_id, action_id) VALUES (:request_id, :entity_id, :action_id)";
                    $req = $pdo->prepare($sql);
                    if (false === $req->execute(['request_id' => $_description->getId_(), 'entity_id' => $entity->getId(), 'action_id' => $action->getId()])) {
                        CliWriter::echoError("Can not declare the relation between the SQL request {$_description->getName_()} and the entity {$entity->getName()} for action {$action->getName()} in the SQLite database ${dbSqlite}.");
                        exit(1);
                    }
                }
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Build the relations between procedures and other elements.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Build the relations between procedures and other elements (SQL requests, tags, fields, parameters and entities).");
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Procedure $_description */
        foreach ($procedureDescriptions as $_description) {

            /* @var string $_tag */
            foreach ($_description->getTags_() as $_tag) {
                $tag = Tag::getByClassAndName(Tag::getFullyQualifiedClassName(), $_tag);
                $sql = "INSERT INTO procedureTag (procedure_id, tag_id) VALUES (:procedure_id, :tag_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['tag_id' => $tag->getId(), 'procedure_id' => $_description->getId_()])) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var \dbeurive\Backend\Database\Entrypoints\Description\Sql $request */
            foreach ($_description->getRequests_() as $_requestName) {
                $request = \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::getByClassAndName_(\dbeurive\Backend\Database\Entrypoints\Description\Sql::getFullyQualifiedClassName_(), $_requestName);
                if (false === $request) {
                    CliWriter::echoError("The SQL request named {$_requestName} is not found, in procedure.");
                    exit(1);
                }
                $sql = "INSERT INTO procedureRequest (procedure_id, request_id) VALUES (:procedure_id, :request_id)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute([
                        'procedure_id' => $_description->getId_(),
                        'request_id' => $request->getId_()])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_field */
            foreach ($_description->getMandatoryInputFields_() as $_field) {
                $fieldName = $_field[ProcedureDescription::KEY_NAME];
                $apiEntryPointDescriptions = $_field[ProcedureDescription::KEY_DESCRIPTION];
                $always = $_field[ProcedureDescription::KEY_ALWAYS];
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $fieldName);


                $params = [  'procedure_id'  => $_description->getId_(),
                    'field_id'      => $field->getId(),
                    'mandatory'     => $always ? 2 : 1,
                    'description'   => is_null($apiEntryPointDescriptions) ? '' : $apiEntryPointDescriptions];
                $sql = "INSERT INTO procedureInputField (procedure_id, field_id, mandatory, description) VALUES (:procedure_id, :field_id, :mandatory, :description)";

                try {
                    $req = $pdo->prepare($sql);
                    if (false === $req->execute($params)) {
                        CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    }
                } catch (\Exception $e) {
                    echo "\nCould not execute the SQL request: ${sql}. The list of parameters is\n" . print_r($params, true) . "\n" .
                        "Procedure name is: " . $_description->getName_() . "\n";
                }
            }

            /* @var array $_field */
            foreach ($_description->getOptionalInputFields() as $_field) {
                $fieldName = $_field[ProcedureDescription::KEY_NAME];
                $apiEntryPointDescriptions = $_field[ProcedureDescription::KEY_DESCRIPTION];
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $fieldName);
                $sql = "INSERT INTO procedureInputField (procedure_id, field_id, mandatory, description) VALUES (:procedure_id, :field_id, :mandatory, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['procedure_id' => $_description->getId_(),
                        'field_id' => $field->getId(),
                        'mandatory' => 0,
                        'description' => is_null($apiEntryPointDescriptions) ? '' : $apiEntryPointDescriptions])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_param */
            foreach ($_description->getMandatoryInputParams_() as $_param) {
                $paramdName = $_param[ProcedureDescription::KEY_NAME];
                $apiEntryPointDescriptions = $_param[ProcedureDescription::KEY_DESCRIPTION];
                $always = $_param[ProcedureDescription::KEY_ALWAYS];
                $sql = "INSERT INTO procedureInputParam (procedure_id, name, mandatory, description) VALUES (:procedure_id, :name, :mandatory, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['procedure_id' => $_description->getId_(),
                        'name' => $paramdName,
                        'mandatory' => $always ? 2 : 1,
                        'description' => is_null($apiEntryPointDescriptions) ? '' : $apiEntryPointDescriptions])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_param */
            foreach ($_description->getOptionalInputParams_() as $_param) {
                $paramdName = $_param[ProcedureDescription::KEY_NAME];
                $apiEntryPointDescriptions = $_param[ProcedureDescription::KEY_DESCRIPTION];
                $sql = "INSERT INTO procedureInputParam (procedure_id, name, mandatory, description) VALUES (:procedure_id, :name, :mandatory, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['procedure_id' => $_description->getId_(),
                        'name' => $paramdName,
                        'mandatory' => 0,
                        'description' => is_null($apiEntryPointDescriptions) ? '' : $apiEntryPointDescriptions])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_field */
            foreach ($_description->getOutputFields_() as $_field) {
                $fieldName = $_field[ProcedureDescription::KEY_NAME];
                $apiEntryPointDescriptions = $_field[ProcedureDescription::KEY_DESCRIPTION];
                $field = Field::getByClassAndName(Field::getFullyQualifiedClassName(), $fieldName);
                $sql = "INSERT INTO procedureOutputField (procedure_id, field_id, description) VALUES (:procedure_id, :field_id, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['procedure_id' => $_description->getId_(),
                        'field_id' => $field->getId(),
                        'description' => is_null($apiEntryPointDescriptions) ? '' : $apiEntryPointDescriptions])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_value */
            foreach ($_description->getOutputDataValues_() as $_value) {
                $valueName = $_value[ProcedureDescription::KEY_NAME];
                $apiEntryPointDescriptions = $_value[ProcedureDescription::KEY_DESCRIPTION];
                $sql = "INSERT INTO procedureOutputDataValue (procedure_id, name, description) VALUES (:procedure_id, :name, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['procedure_id' => $_description->getId_(),
                        ProcedureDescription::KEY_NAME => $valueName,
                        ProcedureDescription::KEY_DESCRIPTION => is_null($apiEntryPointDescriptions) ? '' : $apiEntryPointDescriptions])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_value */
            foreach ($_description->getOutputValues_() as $_value) {
                $valueName = $_value[ProcedureDescription::KEY_NAME];
                $apiEntryPointDescriptions = $_value[ProcedureDescription::KEY_DESCRIPTION];
                $sql = "INSERT INTO procedureOutputValue (procedure_id, name, description) VALUES (:procedure_id, :name, :description)";
                $req = $pdo->prepare($sql);
                if (false === $req->execute(['procedure_id' => $_description->getId_(),
                        ProcedureDescription::KEY_NAME => $valueName,
                        ProcedureDescription::KEY_DESCRIPTION => is_null($apiEntryPointDescriptions) ? '' : $apiEntryPointDescriptions])
                ) {
                    CliWriter::echoError("Can not insert data into SQLite database \"${dbSqlite}\".");
                    exit(1);
                }
            }

            /* @var array $_actionsList */
            foreach ($_description->getEntitiesActions_() as $_entityName => $_actionsList) {
                $entity = \dbeurive\Backend\Database\Entrypoints\Description\Element\Entity::getByClassAndName(\dbeurive\Backend\Database\Entrypoints\Description\Element\Entity::getFullyQualifiedClassName(), $_entityName);
                if (false === $entity) {
                    CliWriter::echoError("The entity named ${_entityName} is not found");
                    exit(1);
                }

                foreach ($_actionsList as $_actionName) {
                    $action = \dbeurive\Backend\Database\Entrypoints\Description\Element\Action::getByClassAndName(\dbeurive\Backend\Database\Entrypoints\Description\Element\Action::getFullyQualifiedClassName(), $_actionName);
                    if (false === $action) {
                        CliWriter::echoError("The action named ${_actionName} is not found");
                        exit(1);
                    }

                    $sql = "INSERT INTO procedureEntityAction (procedure_id, entity_id, action_id) VALUES (:procedure_id, :entity_id, :action_id)";
                    $req = $pdo->prepare($sql);
                    if (false === $req->execute(['procedure_id' => $_description->getId_(), 'entity_id' => $entity->getId(), 'action_id' => $action->getId()])) {
                        CliWriter::echoError("Can not declare the relation between the procedure {$_description->getName_()} and the entity {$entity->getName()} for action {$action->getName()} in the SQLite database ${dbSqlite}.");
                        exit(1);
                    }
                }
            }
        }

        // -------------------------------------------------------------------------------------------------------------
        // Save the PHP/Json representation of the database.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoInfo("   Create the JSON representation of the database in file \"${dbJson}\".");
        if (false === file_put_contents($dbJson, json_encode($rawDatabaseSchema))) {
            CliWriter::echoError("Can not create file \"${dbJson}\".");
            exit(1);
        }

        CliWriter::echoInfo("   Create the PHP representation of the database in file \"${phpDbDescription}\".");
        try {
            UtilData::to_callable_php_file($rawDatabaseSchema, $phpDbDescription);
        } catch (\Exception $e) {
            CliWriter::echoError($e->getMessage());
            exit(1);
        }

        // -------------------------------------------------------------------------------------------------------------
        // Save the PHP representation on the authorization' specifications.
        // -------------------------------------------------------------------------------------------------------------

        CliWriter::echoSuccess("Database representation \"${dbSqlite}\" successfully created.");
        CliWriter::echoSuccess("Database representation \"${dbJson}\" successfully created.");
        CliWriter::echoSuccess("Database representation \"${phpDbDescription}\" successfully created.");
        return true;
    }

}