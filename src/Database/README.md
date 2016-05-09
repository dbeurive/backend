# Description

This directory contains the implementation on the "database interface".

The "database interface" provides:
 
* Access to the database (through the API's entry points).
  Through this interface the application gains access to API's entry points (SQL requests and procedures).
* Information about the database.
  For example, an API's entry point may request the list of fields that compose a given table.
     
# Synopsis

> Since all the PHP code is documented using PhpDoc annotations, you should be able to exploit the auto completion feature from your favourite IDE.
> If you are using Eclipse, NetBeans or PhPStorm, using this API should be pretty intuitive.

Initialize a database interface:

```php
    $di = \dbeurive\Backend\Database\DatabaseInterface::getInstance('default', $configuration);
```
    
Get an API's entry point:
   
```php
    $request = $di->getSql('User/Authenticate');
    $procedure = $di->getProcedure('User/Authenticate'); 
```

Configure and execute the API's entry point:

```php
    $resultSql = $request->setExecutionConfig(['user.login' => 'foo', 'user.password' => 'bar'])
                         ->execute();
            
    $resultProcedure = $procedure->addInputField('user.login', 'foo')
                                 ->addInputField('user.password', 'bar)
                                 ->execute();
```       

Exploit the result:

```php
    if ($resultSql->isSuccess) { ... }
    
    if ($resultProcedure->isSuccess) { ... }
```

# Initialization in details

First you crate a ["database link"](https://github.com/dbeurive/backend/tree/master/src/Database/Link).

```php
$linkConfiguration = [
     'db-host'      => 'localhost',
     'db-name'      => 'MyDatabase',
     'db-user'      => 'admin',
     'db-password'  => 'password'
];
$databaseLink = new \dbeurive\Backend\Database\Link\Mysql();
$errors = $databaseLink->setConfiguration($linkConfiguration);
if (count($errors) > 0) {
    throw new \Exception("Invalid configuration: " . implode(", ", $errors));
}
$databaseLink->connect();
```
 
Then you get an instance of the database interface:

Example 1:

```php
$diConfiguration = [
            'sql-repository-path'          => '/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Sqls',
            'procedure-repository-path'    => '/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Procedures',
            'sql-base-namespace'           => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls',
            'procedure-base-namespace'     => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures',
            'php-db-desc-path'             => '/Users/denisbeurive/php-public/backend/tests/cache/mysql_db_schema.php'
];

$databaseInterface = \dbeurive\Backend\Database\DatabaseInterface::getInstance('default', $diConfiguration);
$databaseInterface->setDbLink($databaseLink);
```

Or, example 2:

```php
$diConfiguration = [
            'sql-repository-path'          => '/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Sqls',
            'procedure-repository-path'    => '/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Procedures',
            'sql-base-namespace'           => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls',
            'procedure-base-namespace'     => '\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures',
            'php-db-desc-path'             => '/Users/denisbeurive/php-public/backend/tests/cache/mysql_db_schema.php',
            'db-link'                      => $databaseLink
];

$databaseInterface = \dbeurive\Backend\Database\DatabaseInterface::getInstance('default', $diConfiguration);
```

Or, example 3:

```php
$databaseInterface = \dbeurive\Backend\Database\DatabaseInterface::getInstance();
$databaseInterface->setSqlRepositoryBasePath($inOptConfig['/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Sqls');
$databaseInterface->setSqlBaseNameSpace($inOptConfig['/Users/denisbeurive/php-public/backend/tests/EntryPoints/Brands/MySql/Procedures');
$databaseInterface->setProcedureRepositoryBasePath('\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Sqls');
$databaseInterface->setProcedureBaseNameSpace($inOptConfig['\\dbeurive\\BackendTest\\EntryPoints\\Brands\\MySql\\Procedures');
$databaseInterface->setPhpDatabaseRepresentationPath($inOptConfig['/Users/denisbeurive/php-public/backend/tests/cache/mysql_db_schema.php');
$databaseInterface->setDbLink($databaseLink);
```

> NOTE: The three examples above are strictly equivalent.

Then, from anywhere in your code, you can retrieve the previously instantiated database interface by its name:

```php
$databaseInterface =  \dbeurive\Backend\Database\DatabaseInterface::getInstance('default');
``` 

> Please note than you can create as many instances of the database interface. Each instance has a name.
> If you don't give a name to a new instance (see example 3), then the name of the instance is "default".
> Creating many database interfaces may be interesting if you use more than one database.
> Or you may want to define specific entry points to enforce security.

