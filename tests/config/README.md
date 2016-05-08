# Description

This directory contains the configuration for the tests.
 
The configuration is defined in the file `config.php`.

The script `configFile2Shell.php` will generate the command line used to produce the documentation for the database access layers.
For example:

```sh
    /Users/php-public/backend/tests/config/../../src/Cli/Bin/backend \
	    db:doc-mysql \
	    -v \
	    --sql-repository-path=/Users/denisbeurive/php-public/backend/tests/config/../EntryPoints/Brands/MySql/Sqls \
	    --procedure-repository-path=/Users/denisbeurive/php-public/backend/tests/config/../EntryPoints/Brands/MySql/Procedures \
	    --sql-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls \
	    --procedure-base-namespace=\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures \
	    --doc-db-repository-path=/Users/denisbeurive/php-public/backend/tests/config/../cache \
	    --doc-db-basename=mysql_db_schema \
	    --php-db-desc-path=/Users/denisbeurive/php-public/backend/tests/config/../cache/mysql_db_schema.php \
	    --db-host=localhost \
	    --db-name=phptools \
	    --db-user=root \
	    --db-port=3306 \
	    --db-link-class-name=\\dbeurive\\Backend\\Database\\Link\\MySql
```
   
The file `MySqlConfLoader.php` implements a *configuration loader*. Using this component the previous command line becomes :

```sh
    /Users/php-public/backend/tests/config/../../src/Cli/Bin/backend db:doc-mysql --config-loader=\\dbeurive\\BackendTest\\config\\MySqlConfLoader
``` 

