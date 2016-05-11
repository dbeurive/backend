# Description

This directory contains the scripts that can be executed from the command line interface.

## Script `backend`

This script can be used to perform the following actions:

   * Extract the schema of the database (option `db:schema-...`).
   * Create the SQLite database that represents the documentation for the database access layer (option `db:doc-...`).

## Examples of use

### Extract the schema of the database

```sh
backend db:schema-mysql \
	-v \
	--db-host=localhost \
	--db-name=phptools \
	--db-user=root \
	--db-port=3306 \
	--schema-path=/Users/denisbeurive/php-public/backend/tests/config/../cache/mysql_schema.php
``` 

| Parameter                 | Description                                                    |
|---------------------------|----------------------------------------------------------------|
| db-host                   | Name of the host that runs the MySql server.                   |
| db-name                   | Name of the database.                                          |
| db-user                   | Name of the user.                                              |
| db-password               | Password fot he user.                                          |
| db-port                   | TCP port used by the server to listen to incoming requests.    |
| schema-path               | Path to the PHP file that will be used to store the schema.    |

### Generate the documentation for all API's entry points (SQL requests and procedures).

```sh
backend db:doc-mysql \
	-v \
	--sql-repository-path=/Users/denisbeurive/php-public/backend/tests/config/../EntryPoints/Brands/MySql/Sqls \
	--procedure-repository-path=/Users/denisbeurive/php-public/backend/tests/config/../EntryPoints/Brands/MySql/Procedures \
	--sql-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls \
	--procedure-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures \
	--doc-path=/Users/denisbeurive/php-public/backend/tests/config/../cache/mysql_doc.sqlite \
	--schema-path=/Users/denisbeurive/php-public/backend/tests/config/../cache/mysql_schema.php
```

| Option                    | Description                                                                                   |
|---------------------------|-----------------------------------------------------------------------------------------------|
| sql-repository-path       | Absolute path to the base directory use to store all SQL requests (PHP) classes.              |
| procedure-repository-path | Absolute path to the base directory use to store all procedures (PHP) classes.                |
| sql-base-namespace        | Base namespace for all SQL requests.                                                          |
| procedure-base-namespace  | Base namespace for all procedures.                                                            |
| doc-path                  | Path to the SQLite database that will be used to store the generated documentation.           |
| schema-path               | Path to the PHP file that contains the schema of the database (the list of fields).           |

## Using a configuration loader

Please note that, if you have an application configuration file somewhere, you don't have to specify all the command line options.
You can create a "configuration loader".
A "configuration loader" is just a class the implements the method `load()`.

Examples:

* [\dbeurive\BackendTest\config\MySqlSchemaConfLoader](https://github.com/dbeurive/backend/blob/master/tests/config/MySqlSchemaConfLoader.php)
* [\dbeurive\BackendTest\config\MySqlDocConfLoader](https://github.com/dbeurive/backend/blob/master/tests/config/MySqlDocConfLoader.php)


Using the configuration loader is pretty simple:

```sh
backend db:schema-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlSchemaConfLoader
```

Or

```sh
backend db:doc-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlDocConfLoader
```
