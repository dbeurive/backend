# Description

This document describes the interface used to document an API's entry point (an SQL request or a procedure).

The description for an API's entry point is composed of:

* A description that is common to all API's entry points. See [Description/AbstractDescription.php](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/AbstractDescription.php).
* A specific description.
  * For SQL requests: see [Description/Sql.php](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/Sql.php).
  * For procedures: see [Description/Procedure.php](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/Procedure.php).




# Vocabulary

**Data value** (for SQL requests and procedures)

> A data value is a value that is calculated and returned by the database server (and NOT by the PHP code).
> A data value is NOT a field's value.
> 
> Example: `SELECT count(id) AS number FROM user`. `number` is a data value.     
 
**Parameter value** (for SQL requests only)

> A value used to parameter an SQL request.
> 
> Example: `SELECT * FROM user LIMIT 10`. 10 is a parameter value.

**Output value** (for procedures only)

> A value that has been calculated by the PHP code within a procedure.
> An "output value" is not returned by the SGBDR. Values returned by the SGBDR are called "data values".





# Entry points' descriptions

## The description common to all API's entry points

*  The name of the API's entry point.
*  A textual description.
*  A list of tags.
*  A list of (output) data values (See "Vocabulary").
*  A list of entity-action relationships.

See class [\dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/AbstractDescription.php).




## Description for SQL requests

* The list of tables used by the request.
* The list of selected fields.
* The list of updated fields.
* The list of fields that are used within _conditions_ (`WHERE ...`).
* The list of inserted fields.
* The list of "upserted" fields.
* The list of fields used to organise the request's result (`ORDER BY id`, for example).
* The list of parameters' values (See "Vocabulary").
* The type of request ("select", "update", "insert", "delete", "upsert").
* The SQL request itself. Please note that you are free to choose your own formalism. Plain text should be enough.
  But in rare occasions, you may want to use another formalism. 
 
See class [\dbeurive\Backend\Database\Entrypoints\Sql.php\Description](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/Sql.php).




## Description for procedures

* The list of SQL requests used by the procedure.
* The list of mandatory fields.
* The list of optional fields.
* The list of mandatory parameters.
* The list of optional parameters.
* The list of output fields.
* The list of output values (See "Vocabulary").
* A flag that indicates whether the result of the procedure is an array of rows or not.
  
Note: 
  
> Please note that a procedure's configuration should not be complex.
> However, in practice it could be (_most likely due to a bad design_):
> Some fields or parameters may be mandatory depending on a context of execution.
> Therefore, the API does not require the user to declare fields or parameters to be always mandatory.
> You should declare as optional, fields or parameters that are **always optional**.
> If a field or a parameter _may be mandatory_ (depending on the context of execution) you should declare it "occasionally mandatory".
> You do that by calling the method `addMandatoryInputParam(..., ..., false)`.




## Summary

| SQL requests                                                             | Procedures                                                                                 |
|--------------------------------------------------------------------------|--------------------------------------------------------------------------------------------|
| The name of the entry point.                                             | The name of the entry point.                                                               |
| A textual description.                                                   | A textual description.                                                                     |
| A list of tags.                                                          | A list of tags.                                                                            |
| A list of (output) data values (See "Vocabulary").                       | A list of (output) data values (See "Vocabulary").                                         |
| A list of entity-action relationships.                                   | A list of entity-action relationships.                                                     |
| The list of tables used by the request.                                  | The list of SQL requests used by the procedure.                                            |
| The list of selected fields.                                             | The list of mandatory fields.                                                              |
| The list of updated fields.                                              | The list of optional fields.                                                               |
| The list of fields that are used within _conditions_.                    | The list of mandatory parameters.                                                          |
| The list of inserted fields.                                             | The list of optional parameters.                                                           |
| The list of "upserted" fields.                                           | The list of output fields.                                                                 |
| The list of fields used to organise the request's result.                | The list of output values] (See "Vocabulary").                                             |
| The list of parameters' values (See "Vocabulary").                       | A flag that indicates whether the result of the procedure is an array of rows or not.      |
| The type of request ("select", "update", "insert", "delete", "upsert").  |                                                                                            |
| The SQL request itself.                                                  |                                                                                            |




# Synopsis

## Document an SQL request

Note: this is a fictional example.

```php
    public function getDescription() {

        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
        $doc->setDescription('This request checks that the authentication data is valid.')
            ->addTags('authentication')
            ->addOutputDataValue('authenticated', 'This value indicates whether the user is authenticated or not.')
            ->addEntityActionsRelationship('user', 'authenticate')
            ->setType('select')
            ->setSql('SELECT 1 AS authenticated, `user`.`id`, `user`.`login`, `user`.`password` FROM USER WHERE `user`.`login`=? AND `user`.`password`=?')
            ->addTable('user')
            ->setSelectedFields(['user.*']) // <=> ->setSelectedFields($this->_getTableFieldsNames('user', self::FIELDS_FULLY_QUALIFIED_AS_ARRAY, false))
            ->setConditionFields(['user.login', 'user.password']);

        return $doc;
    }
```

See [examples](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Sqls/User).

Please note that you should use constants to define elements of documentation (tags, entities' names...).
By using constants:

* You ensure that you don't make any typo.
* You can rely on your IDE's search capacity.

## Document a procedure

```php
    public function getDescription() {
    
        $doc = new Description\Procedure();
        $doc->setDescription("This procedure is used to authenticate a user based on a provided set of login and password.")
            ->setRequests(['User/Authenticate'])
            ->addTags('authentication')
            ->setMandatoryInputFields([['user.login'], ['user.password']])
            ->addOutputField('user.*')
            ->addOutputDataValue('authenticated', 'This flag indicates whether the user has been successfully authenticated or not. TRUE: authentication succeed, FALSE: authentication failed.')
            ->addEntityActionsRelationship('user', 'authenticate');

        return $doc;
    }
```

See [examples](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Procedures/User).

Please note that you should use constants to define elements of documentation (tags, entities' names...).
By using constants:

* You ensure that you don't make any typo.
* You can rely on your IDE's search capacity.

