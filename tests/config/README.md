# Introduction

This directory contains the configuration for the tests.
 
The configuration is defined in the file `config.php`.




# Configuring the test

Before running the test suite, you must edit the file `config.php`.

You must set the values below according to your test environment:

```php
    $mysqlConf = [
        MySqlPdo::DB_HOST      => 'localhost',
        MySqlPdo::DB_NAME      => 'phptools', // !!! WARNING !!! See the file "tests/fixtures/MySql/schema.php"
        MySqlPdo::DB_USER      => 'root',
        MySqlPdo::DB_PASSWORD  => '',
        MySqlPdo::DB_PORT      => 3306
    ];
```

Note 1:

> **WARNING**: You can change the name of the database. However, if you do so, then you must also modify the script that creates the database.
> See the file [tests/fixtures/MySql/schema.php](https://github.com/dbeurive/backend/blob/master/tests/fixtures/MySql/schema.php).

Note 2:

> Make sure that the user has the permission to create a database. The database used by these tests will be dropped and re-created for each test.





# The script `configFile2Shell.php`

The script `configFile2Shell.php` will generate the command line used to produce the documentation for the database access layers.

## Generating the command line for testing the creation of the PHP file that contains the schema of the database

```sh
$ php configFile2Shell.php db:schema-mysql
/path/to/the/script/backend \
	db:schema-mysql \
	-v \
	--db-host=localhost \
	--db-name=phptools \
	--db-user=root \
	--db-port=3306 \
	--schema-path=/Users/denisbeurive/php-public/backend/tests/cache/mysql_schema.php
``` 

The file [`MySqlSchemaConfLoader.php`](https://github.com/dbeurive/backend/blob/master/tests/config/MySqlSchemaConfLoader.php) implements a *configuration loader*. Using this component the previous command line becomes:

```sh
/path/to/the/script/backend db:schema-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlSchemaConfLoader
```

## Generating the command line for testing the creation of the SQLite database that contains the documentation of all SQL requests and procedures

For example:

```sh
$ php configFile2Shell.php db:doc-writer
/path/to/the/script/backend \
	db:doc-writer \
	-v \
	--sql-repository-path=/Users/denisbeurive/php-public/backend/tests//EntryPoints/Brands/MySql/Sqls \
	--procedure-repository-path=/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Procedures \
	--sql-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls \
	--procedure-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures \
	--doc-path=/Users/denisbeurive/php-public/backend/tests/cache/mysql_doc.sqlite \
	--schema-path=/Users/denisbeurive/php-public/backend/tests/cache/mysql_schema.php
```
   
The file [`MySqlDocConfLoader.php`](https://github.com/dbeurive/backend/blob/master/tests/config/MySqlDocConfLoader.php) implements a *configuration loader*. Using this component the previous command line becomes:

```sh
/path/to/the/script/backend db:doc-writer --config-loader=\\dbeurive\\BackendTest\\config\\MySqlDocConfLoader
``` 

