# Description

This namespace defines a *database link*.

A *database link* holds a low level database connexion handler (such as PDO) along with specific methods.

These specific methods :

* Extends, or just expose (depending on the level database connexion handler), the functionalities of the low level database connexion handler.
  * `function quoteValue($inValue)`
  * `function quoteFieldName($inFieldName)`
  * `function getDatabaseSchema()`
  * `function getErrorCode()`
  * `function getErrorMessage()`
* Allows transparent integration of the low level database connexion handler within the main configuration.
  * `function getConfigurationOptions()`

Each *database link* defines its own set of configuration parameters.

## Default MySql database link
  
This database link for MySql uses PDO as database connexion handler. Configuration parameters for this *database link* are:

| Parameter    | Description                                                       |
|--------------|-------------------------------------------------------------------|
| db-host      | Name of the host that runs the MySql server.                      |
| db-name      | Name of the database.                                             |
| db-user      | User's identifier use to authenticate to the server.              | 
| db-password  | User's password.                                                  |
| db-port      | TCP port used by the MySql server to listen to incoming requests. |

See [Configuration parameters for the MySql database link](https://github.com/dbeurive/backend/blob/master/src/Database/Link/MySql.php)

