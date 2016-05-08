# Inroduction

This document describes the interface used to document an API's entry point.

API's entry points can be:

* SQL requests.
* Procedures.

The description for an API's entry point is composed of:

* A description that is common to all API's entry points.
* A specific description.
** For SQL requests, the description is implemented within the file `dbeurive\Backend\Database\Entrypoints\Description\Sql.php`.
** For procedures, the description is implemented within the file `dbeurive\Backend\Database\Entrypoints\Description\Procedure.php`.

# The description common to all API's entry points

*  The name of the API's entry point.
*  A description of the API's entry point. See method `setDescription($inDescription)`.
*  A list of tags. See method `addTags($tag1[, $tag2...])`.
*  The list of (output) *data values* (other than fields' values) returned by the API's entry point. See method `addOutputDataValue($inValueName, $inValueDescription=null)`.
   Please note that a *data value* is a value that is calculated and returned **by the RDBMS** (and **NOT** by the PHP code).
   A *data value* is **NOT** a field's value.
*  The list of entity-action relationship associated to the API's entry point. See method `addEntityActionsRelationship($inEntityName, $inAction)`

Common API:

    setDescription($inDescription)
    addOutputDataValue($inValueName, $inValueDescription=null)
    addEntityActionsRelationship($inEntityName, $inAction)
    addTags($tag1[, $tag2...])

Please note that there is a method `setName_()` used to set the name of the API's entry point.
But this method is called automatically by the script that extracts the description from the API's entry points.

Please note that the developer can use the method `_getTableFieldsNames($inTableName, $inFormat=self::FIELDS_RAW_AS_ARRAY, $inOptQuote=true)`.
This method returns the list of all fields within a given table, in different forms.

* If `$inFormat=self::FIELDS_RAW_AS_ARRAY`: the list of fields' names is returned as an array of strings.
  Each element of the returned array is a string "\<field name\>". For example: ["id", "login", "password"]
* If `$inFormat=self::FIELDS_FULLY_QUALIFIED_AS_ARRAY`: the list of fully qualified fields' names is returned as an array of strings.
  Each element of the returned array is a string "\<table name\>.\<field name\>".
  For example:
  * if `$inOptQuote=true`: ["\`user\`.\`id\`", "\`user\`.\`login\`", "\`user\`.\`password\`"]
  * if `$inOptQuote=false`: ["user.id", "user.login", "user.password"]
* If `$inFormat=self::FIELDS_FULLY_QUALIFIED_AS_SQL`: the list of fields' names is returned as a string "\<table name\>.\<field name\> as '\<table name\>.\<field name\>',...".
  For example:
  * if `$inOptQuote=true`: "\`user\`.\`id\` AS 'user.id', \`user\`.\`login\` AS 'user.login', \`user\`.\`password\` AS 'user.password'"
  * if `$inOptQuote=false`: "user.id AS 'user.id', user.login AS 'user.login', user.password AS 'user.password'"

> The method `_getTableFieldsNames($inTableName, $inFormat=self::FIELDS_RAW_AS_ARRAY, $inOptQuote=true)` is useful to format SQL requests.
  With this method you don't have to enumerate all the fields within a given table. For example:

    private function __getSql() {
        $sql = preg_replace('/__USERS__/', $this->_getTableFieldsNames('user', self::FIELDS_FULLY_QUALIFIED_AS_SQL), self::$__sql);
        return $sql;
    }

# Specific description for SQL requests

Description for SQL requests includes:

* The list of tables used by the SQL request. See methods `addTable($inTable)` and `setTables(array $inTables)`.
* The list of fields selected by the SQL request. See methods `addSelectedField($inField)` and `setSelectedFields(array $inFields)`.
* The list of fields updated by the SQL request. See methods `addUpdatedField($inField)` and `setUpdatedFields(array $inFields)`.
* The list of fields used within conditions by the SQL request. See methods `addConditionField($inField)` and `setConditionFields(array $inFields)`.
* The list of fields used to organize the returned data. See methods `addPresentationField(array $inFields)` and `setPresentationFields(array $inFields)`.
* The type of the SQL request (UPDATE, SELECT, INSERT or DELETE). See method `setType($inType)`.
* The SQL request, or its template. See method `setSql($inType)`.

API for SQL requests:

    addTable($inTable)
    setTables(array $inTables)
    addSelectedField($inField)
    setSelectedFields(array $inFields)
    addUpdatedField($inField)
    setUpdatedFields(array $inFields)
    addConditionField($inField)
    setConditionFields(array $inFields)
    addPresentationField($inField)
    setPresentationFields(array $inFields)
    setType($inType)
    setSql($inType)

Example of use:

    class Authenticate extends AbstractEntryPoint {
        // ...
        public function getDescription() {
            $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
            $doc->setDescription('This request checks that the authentication data is valid.')
                ->addTags('authentication')
                ->addEntityActionsRelationship(Entities::USER, Actions::SELECT)
                ->setType($doc::TYPE_SELECT)
                ->setSql('SELECT `user`.`id`, `user`.`login` FROM   `user` WHERE  `user`.`login`=? AND  `user`.`password`=?')
                ->addTable('user')
                ->setSelectedFields(['user.*'])
                ->setConditionFields(['user.login', 'user.password']);
            return $doc;
        }
    }

# Specific description for procedures

Description for procedures includes:

* The list of SQL requests used by the procedure. See methods `addRequest($inRequest)` and `setRequests(array $inRequests)`.
* The list of mandatory fields used as input by the procedure. Please note that a procedure's configuration should not be complex.
  However, in practice it could be (*most likely due to a bad design*) : some fields may be mandatory depending on a context of execution.
  Therefore, the API does not require the user to declare fields to be **always** mandatory.
  See methods `addMandatoryInputField($inFieldName, $inDescription=null, $inOptAlwaysMandatory=true)` and `setMandatoryInputFields(array $inFields)`.
* The list of optional fields used as input by the procedure. Please note that a procedure's configuration should not be complex.
  Optional fields should always be optional. However, probably due to bad design, this may not be the case.
  You should declare as optional fields that are **always** optional.
  If a field may be mandatory (depending on the context of execution) you should declare it "*occasionally mandatory*".
  You do that by calling the method `addMandatoryInputField(..., ..., false)`.
  See methods `addOptionalInputField($inFieldName, $inDescription=null)` and `setOptionalInputFields(array $inFields)`.
* A list of mandatory input parameters used by the procedure. Please note that a procedure's configuration should not be complex.
  However, in practice it could be (*most likely due to a bad design*) : some parameters may be mandatory depending on a context of execution.
  Therefore, the API does not require the user to declare parameters to be always mandatory.
  See methods `addMandatoryInputParam($inParamName, $inDescription=null, $inOptAlwaysMandatory=true)` and `setMandatoryInputParams(array $inParams)`.
* A list of optional input parameters used by the procedure. Please note that a procedure's configuration should not be complex.
  Optional parameters should always be optional. However, probably due to bad design, this may not be the case.
  You should declare as optional parameters that are always optional.
  If a parameter may be mandatory (depending on the context of execution) you should declare it "*occasionally mandatory*".
  You do that by calling the method `addMandatoryInputParam(..., ..., false)`.
  See methods `addOptionalInputParam($inParamName, $inDescription=null)` and `setOptionalInputParams(array $inParams)`.
* The list of fields returned by the procedure.
  See methods `addOutputField($inFieldName, $inDescription=null)` and `setOutputFields(array $inFields)`.
* The list of values returned by the procedure. Please note that a value is a value that is calculated **by the procedure** (that is, by the PHP code).
  A value is **NOT** returned by the RDBMS. Values returned by the RDBMS are called *data values*.
  See methods `addOutputValue($inValueName, $inValueDescription=null)` and `setOutputValues(array $inValues)`.

API for procedures:

    addRequest($inRequest)
    setRequests(array $inRequests)
    addMandatoryInputField($inFieldName, $inDescription=null, $inOptAlwaysMandatory=true)
    setMandatoryInputFields(array $inFields)
    addOptionalInputField($inFieldName, $inDescription=null)
    setOptionalInputFields(array $inFields)
    addMandatoryInputParam($inParamName, $inDescription=null, $inOptAlwaysMandatory=true)
    setMandatoryInputParams(array $inParams)
    addOptionalInputParam($inParamName, $inDescription=null)
    setOptionalInputParams(array $inParams)
    addOutputField($inFieldName, $inDescription=null)
    setOutputFields(array $inFields)
    addOutputValue($inValueName, $inValueDescription=null)
    setOutputValues(array $inValues)
    
Example of use:

    class Authenticate extends AbstractEntryPoint {
        public function getDescription() {
            $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Procedure();
            $doc->setDescription("This procedure is used to authenticate a user based on a provided set of login and password.")
                ->setRequests(['user/Authenticate'])
                ->addTags('Authentication')
                ->setMandatoryInputFields([['user.login', "The user's login"], ['user.password']])
                ->addOutputField('user.*') // <=> ->setSelectedFields($this->_getTableFieldsNames('user', self::FIELDS_FULLY_QUALIFIED_AS_ARRAY, false))
                ->addOutputDataValue('isAuthenticated', 'This flag indicates whether the user has been successfully authenticated or not.')
                ->addEntityActionsRelationship('user', 'authenticate');
            return $doc;
        }
    }
    
    
    
    