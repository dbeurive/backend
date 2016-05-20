# Introduction

API's entry points are:

* SQL requests.
* Procedures.

Accessing the database is a three-step process:

* The application calls the procedures. 
* The procedures call the SQL requests. 
* The SQL requests call the low level database connection handler.

You can think about traditional databases' stored procedures that act as an access layer for data management, except that we implement this access layer using the PHP language.

> Although the application can access the database directly from using the SQL requests, it should, however, use the procedures.




# Synopsis

> Since all the PHP code is documented using PhpDoc annotations, you should be able to exploit the auto completion feature from your favourite IDE.
> If you are using Eclipse, NetBeans or PhPStorm, using this API should be pretty intuitive.



## Writing an SQL request

All SQL requests share a common (prefixed) namespace and a common (prefixed) path.

In this example, we decide that:

* Base namespace: `dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls`
* Base path: `/backend/tests/EntryPoints/Brands/MySql/Sqls`

> Fully qualified class names and paths of the classes that implement SQL requests must follow the [PSR4 specification](http://www.php-fig.org/psr/psr-4/).

| Path to the class that implements the SQL request                  | Fully qualified name of the class                                     | Name of the request |   
|--------------------------------------------------------------------|-----------------------------------------------------------------------|---------------------|
| /backend/tests/EntryPoints/Brands/MySql/Sqls/User/Authenticate.php | \dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User\Authenticate | User/Authenticate   |
| /backend/tests/EntryPoints/Brands/MySql/Sqls/User/Delete.php       | \dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User\Delete       | User/Delete         |
| ...                                                                | ...                                                                   | ...                 |


Below, we create the SQL request which name will be `User/Authenticate`.

```php
namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;
use dbeurive\Backend\Database\EntryPoints\AbstractSql;

class Authenticate extends AbstractSql
{
    // Execute request.
    public function execute($inExecutionConfig) {
    
        // Retrieve the database handler.
        // What you get is what you set during the configuration of the database interface.
        // Here, we suppose that we've set an instance of \PFO.
        // However, you can set whatever database handler that you may think about.
        
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();
        
        // ...
        
        // Return the result. You are free to return the type of data you want.
        return $result;
    }

    // Document the request.
    public function getDescription() {
        $documentation = new \dbeurive\Backend\Database\EntryPoints\Description\Sql();

        // Add information to the documentation.

        return $documentation;
    }
```

See:

* Documentation
  * [Documenting an SQL request](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/README.md)
* Useful utility functions:
  * [UtilMySql::developSql()](https://github.com/dbeurive/util/blob/master/README.md)
* Examples
  * [Examples of SQL requests](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Sqls/User)
* Code
  * [API common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractEntryPoint.php)
  * [API for an SQL request](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractSql.php)
  * [Elements of documentation common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/AbstractDescription.php)
  * [Elements of documentation specific to SQL requests](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/Sql.php)



## Writing a procedure

All SQL requests share a common (prefixed) namespace and a common (prefixed) path.

In this example, we decide that:

* Base namespace: `dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures`
* Base path: `/backend/tests/EntryPoints/Brands/MySql/Procedures`

> Fully qualified class names and paths of the classes that implement procedures must follow the [PSR4 specification](http://www.php-fig.org/psr/psr-4/).

| Path to the class that implements the procedure                          | Fully qualified name of the class                                           | Name of the procedure |   
|--------------------------------------------------------------------------|-----------------------------------------------------------------------------|-----------------------|
| /backend/tests/EntryPoints/Brands/MySql/Procedures/User/Authenticate.php | \dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User\Authenticate | User/Authenticate     |
| /backend/tests/EntryPoints/Brands/MySql/Procedures/User/Delete.php       | \dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User\Delete       | User/Delete           |
| ...                                                                      | ...                                                                         | ...                   |

Below, we create the procedure which name will be `User/Authenticate`.

```php
namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User;
use dbeurive\Backend\Database\EntryPoints\AbstractProcedure;

class Authenticate extends AbstractProcedure {

    const SQL_AUTHENTICATE = 'User/Authenticate';

    // Execute the procedure.
    public function execute($inExecutionConfig) {
    
        // Retrieve a SQL request.
        $sql = $this->getSql(self::SQL_AUTHENTICATE);
        
        // ...
        
        // Execute the request.
        $requestConfiguration = [ /* ... */ ];
        $resultSql = $sql->execute($requestConfiguration);
    
        // ...
        
        // Return the result. You are free to return the type of data you want.
        return $result;
    }

    // Document the procedure/
    public function getDescription() {
        $documentation = new Description\Procedure();

        // Add information to the documentation.

        return $documentation;
    }
}
```

See:

* Documentation
  * [Documenting a procedure](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/README.md)
* Examples
  * [Examples of procedures](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Procedures/User)
* Code
  * [API common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractEntryPoint.php)
  * [API for an SQL request](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractSql.php)
  * [Elements of documentation common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/AbstractDescription.php)
  * [Elements of documentation specific to procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractProcedure.php)






## Calling an SQL request from a procedure

```php

    // Execute the procedure.
    public function execute($inExecutionConfig) {
    
        // Retrieve a SQL request.
        $sql = $this->getSql(self::SQL_AUTHENTICATE);
        
        // ...
        
        // Execute the request.
        $requestConfiguration = [ /* ... */ ];
        $resultSql = $sql->execute($requestConfiguration);
    
        // ...
        
        // Return the result. You are free to return the type of data you want.
        $result;
    }
    
```

> Please note that:
> * You are free to pass any kind of data as execution configuration (`$inExecutionConfig`).
> * You are free to return any kind of object. 

See [examples](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Procedures/User)




## Calling an SQL request from the application

We assume that `$dataInterface` is an instance of the database interface.

```php
$request = $dataInterface->getSql('User/Authenticate');
$result  = $request->execute(['user.login' => 'toto', 'user.password' => 'titi']);
```

> Please note that:
> * You are free to pass any kind of data for `$request->execute(...)`.
> * The type of the value returned by the SQL request depends on you. You are free to return any type of data from SQL requests. 

See [examples](https://github.com/dbeurive/backend/blob/master/tests/EntryPoints/Brands/MySql/Sqls/User/Authenticate.php).




## Calling a procedure from the application

We assume that `$dataInterface` is an instance of the database interface.

```php
$procedure = $dataInterface->getProcedure('User/Authenticate');
$result    = $procedure->execute(['user.login' => 'foo', 'user.password' => 'bar']);
```

> Please note that
> * You are free to pass any kind of data for `$procedure->execute(...)`.
> * The type of the value returned by the procedure depends on you. You are free to return any type of data from procedures. 

See [examples](https://github.com/dbeurive/backend/blob/master/tests/EntryPoints/Brands/MySql/Procedures/User/Authenticate.php).




