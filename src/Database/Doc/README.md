# Description

This directory contains the *documentation writer* component. This component performs the following actions:

  * Extract information from the database (the schema).
  * Extract information from all the API's entry points (SQL requests and procedures).
  * Organize the information previously extracted (into an SQLite database).

# Configuration

The configuration for this component an associative array with the following keys: 

| Parameter                 | Description                                                                                   |
|---------------------------|-----------------------------------------------------------------------------------------------|
| sql-repository-path       | Absolute path to the base directory use to store all SQL requests (PHP) classes.              |
| procedure-repository-path | Absolute path to the base directory use to store all procedures (PHP) classes.                |
| sql-base-namespace        | Base namespace for all SQL requests.                                                          |
| procedure-base-namespace  | Base namespace for all procedures.                                                            |
| doc-db-repository-path    | Path to the directory used to store the generated documentation.                              |
| doc-db-basename           | Base name of the files used to store the generated JSON and SQLite documentation.             |
| php-db-desc-path          | Name of the file used to store the generated PHP documentation.                               |
| db-link-class-name        | Name of the database CLI adaptor that handler the specific database's brand's name.           |
| db-link-config            | Configuration for the database CLI adaptor that handler the specific database's brand's name. |

The parameter `db-link-config` is an associative array which content depends on the specific database link used.
For example, il we use MySql, then the parameter `db-link-config` points to an array that contains the following keys:

| Parameter                 | Description                                                    |
|---------------------------|----------------------------------------------------------------|
| db-host                   | Name of the host that runs the MySql server.                   |
| db-name                   | Name of the database.                                          |
| db-user                   | Name of the user.                                              |
| db-password               | Password fot he user.                                          |
| db-port                   | TCP port used by the server to listen to incoming requests.    |

Real example:

```php
[
    'sql-repository-path'           => '/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Sqls',
    'procedure-repository-path'     => '/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Procedures',
    'sql-base-namespace'            => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls',
    'procedure-base-namespace'      => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures',
    'doc-db-repository-path'        => '/Users/denisbeurive/php-public/backend/tests/cache',
    'doc-db-basename'               => 'mysql_db_schema',
    'php-db-desc-path'              => '/Users/denisbeurive/php-public/backend/tests/cache/mysql_db_schema.php',
    'db-link-class-name'            => '\\dbeurive\\Backend\\Database\\Link\\MySql',
    'db-link-config'                => [
        'db-host'       => 'localhost',
        'db-name'       => 'test-base',
        'db-user'       => 'root',
        'db-password'   => ''
        'db-port'       => 3306
    ] 
]
```

See:

* [Parameters that define the organisation of API's entry points](https://github.com/dbeurive/backend/blob/master/src/Database/Doc/Option.php)
* [Parameters used to configure the database connexion handler](https://github.com/dbeurive/backend/blob/master/src/Database/Link/Option.php)
* [Parameters that define the organisation of the generated documentation](https://github.com/dbeurive/backend/blob/master/src/Database/Doc/Option.php)
* [Parameters used to configure the MySql connexion handler](https://github.com/dbeurive/backend/blob/master/src/Database/Link/MySql.php)

