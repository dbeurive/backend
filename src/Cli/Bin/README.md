# Description

This directory contains the scripts that can be executed from the command line interface.

## Script `backend`

This script creates the SQLite database that represents the documentation for the database access layer.

## Example of use

```sh
/Users/denisbeurive/php-public/backend/src/Cli/Bin/backend \
	db:doc-mysql \
	-v \
	--sql-repository-path=/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Sqls \
	--procedure-repository-path=/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Procedures \
	--sql-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls \
	--procedure-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures \
	--doc-db-repository-path=/Users/denisbeurive/php-public/backend/tests//cache \
	--doc-db-basename=mysql_db_schema \
	--php-db-desc-path=/Users/denisbeurive/php-public/backend/tests/cache/mysql_db_schema.php \
	--db-host=localhost \
	--db-name=phptools \
	--db-user=root \
	--db-port=3306 \
	--db-link-class-name=\\dbeurive\\Backend\\Database\\Link\\MySql
```
The following options are mandatory:

| Option                      | Description                                                                                   |
|-----------------------------|-----------------------------------------------------------------------------------------------|
| --sql-repository-path       | Absolute path to the base directory use to store all SQL requests (PHP) classes.              |
| --procedure-repository-path | Absolute path to the base directory use to store all procedures (PHP) classes.                |
| --sql-base-namespace        | Base namespace for all SQL requests.                                                          |
| --procedure-base-namespace  | Base namespace for all procedures.                                                            |
| --doc-db-repository-path    | Path to the directory used to store the generated documentation.                              |
| --doc-db-basename           | Base name of the files used to store the generated JSON and SQLite documentation.             |
| --php-db-desc-path          | Name of the file used to store the generated PHP documentation.                               |
| --db-link-class-name        | Name of the database CLI adaptor that handler the specific database's brand's name.           |

The following options are specific to the MySql "database link" (specified by the script's parameter `db:doc-mysql`):

| Parameter                 | Description                                                    |
|---------------------------|----------------------------------------------------------------|
| db-host                   | Name of the host that runs the MySql server.                   |
| db-name                   | Name of the database.                                          |
| db-user                   | Name of the user.                                              |
| db-password               | Password fot he user.                                          |
| db-port                   | TCP port used by the server to listen to incoming requests.    |

As a result, the script will produce the following file:

* `/Users/denisbeurive/php-public/backend/tests//cache/mysql_db_schema.json` ([example](https://github.com/dbeurive/backend/blob/master/tests/cache/mysql_db_schema.json))
* `/Users/denisbeurive/php-public/backend/tests//cache/mysql_db_schema.php` ([example](https://github.com/dbeurive/backend/blob/master/tests/cache/mysql_db_schema.php))
* `/Users/denisbeurive/php-public/backend/tests//cache/mysql_db_schema.sqlite` ([example](https://github.com/dbeurive/backend/blob/master/tests/cache/mysql_db_schema.sqlite))
 
The `JSON` file and the `PHP` file contains the list of fields within the database.

The SQLite database represents the documentation of the database access layer. See the schema of this database [here](https://github.com/dbeurive/backend/blob/master/src/Database/Doc/schema.php).


Please note that, if you have an application configuration file somewhere, you don't have to specify all the command line options.
You can create a "configuration loader". A "configuration loader" is just a class the implements the method `load()`.

See an example of "configuration loader": [\dbeurive\BackendTest\config\MySqlConfLoader](https://github.com/dbeurive/backend/blob/master/tests/config/MySqlConfLoader.php)

Using the configuration loader is pretty simple:

```sh
backend db:doc-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlConfLoader
```


