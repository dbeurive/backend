# Description

This document describes the interface used to document an API's entry point (an SQL request or a procedure).

The description for an API's entry point is composed of:

* A description that is common to all API's entry points. See [Description/AbstractDescription.php](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/AbstractDescription.php).
* A specific description.
  * For SQL requests: see [Description/Sql.php](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/Sql.php).
  * For procedures: see [Description/Procedure.php](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/Procedure.php).




# Vocabulary

| Name            | Definition                                                                                                
|-----------------|-----------------------------------------------------------------------------------------------------------|
| Data value      | A data value is a value that is calculated and returned by the database server (and NOT by the PHP code). |
|                 | A data value is NOT a field's value.                                                                      |
|                 | `SELECT count(id) AS number FROM user`. `number` is a data value.                                         |
| Parameter value | A value used to parameter an SQL request.                                                                 |
|                 | `SELECT * FROM user LIMIT 10`. 10 is a parameter value.                                                   |




# The description common to all API's entry points

*  The name of the API's entry point.
*  A description of the API's entry point.
*  A list of tags.
*  A list of (output) *data values*.
*  A list of entity-action relationship.







    