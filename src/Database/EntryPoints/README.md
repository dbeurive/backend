# introduction

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

All SQL requests share a common (prefixed) namespace.
In this example, we decide that the namespace for all SQL requests start with `dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls`.
`dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls` is the "base namespace" for SQL request.

> Fully qualified names and paths of the classes that implement SQL requests follow the [PSR4 specification](http://www.php-fig.org/psr/psr-4/).
> Therefore, all classes that implement SQL requests are stored under a common directory.

Specify the namespace:

```php
namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User;
```

Then create the class:

```php
use dbeurive\Backend\Database\EntryPoints\AbstractSql;

class Authenticate extends AbstractSql
{
    // Execute request.
    public function execute($inExecutionConfig) {
        /* @var \PDO $pdo */
        $pdo = $this->getDbh();
        
        // ...
    }

    // Document the request.
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\EntryPoints\Description\Sql();

        /* ... */

        return $doc;
    }
```

See:

* [API common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractEntryPoint.php)
* [API for an SQL request](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractSql.php)
* [Elements of documentation common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/AbstractDescription.php)
* [Elements of documentation specific to SQL requests](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/Sql.php)
* [Examples of SQL requests](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Sqls/User)




## Writing a procedure

All procedures share a common (prefixed) namespace.
In this example, we decide that the namespace for all procedures start with `dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures`.
`dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures` is the "base namespace" for procedures.

> Fully qualified names and paths of the classes that implement procedures follow the [PSR4 specification](http://www.php-fig.org/psr/psr-4/).
> Therefore, all classes that implement procedures are stored under a common directory.

Specify the namespace:

```php
namespace dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User
```

Then create the class:

```php
use dbeurive\Backend\Database\EntryPoints\AbstractProcedure;

class Authenticate extends AbstractProcedure {

    const SQL_AUTHENTICATE = 'User/Authenticate';

    // Execute the procedure.
    public function execute($inExecutionConfig) { /* ... */ }

    // Document the procedure/
    public function getDescription() {
        $doc = new Description\Procedure();

        /* ... */

        return $doc;
    }
}
```

* [API common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractEntryPoint.php)
* [API for a procedure](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractProcedure.php)
* [Elements of documentation common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/Description/AbstractDescription.php)
* [Elements of documentation specific to procedures](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/AbstractProcedure.php)
* [Examples of procedures](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Procedures/User)





## Calling an SQL request from a procedure

```php

    public function execute($inExecutionConfig) {
        $sql = $this->getSql(self::SQL_AUTHENTICATE);
        $resultSql = $sql->execute($inExecutionConfig);
        $result = new ProcedureResult(ProcedureResult::STATUS_SUCCESS,
            $resultSql->getDataSets(),
            [self::KEY_AUTHENTICATED => ! $resultSql->isDataSetsEmpty()]
        );
        return $result;
    }
    
```

> Please note that:
> * You are free to pass any kind of data as execution configuration (`$inExecutionConfig`).
> * You are free to return any kind of object. In this example we return an instance of `\dbeurive\BackendTest\EntryPoints\Result\ProcedureResult`.
>   However, you can define your own class for holding a result.

See [examples](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Procedures/User)




## Calling an SQL request from the application

We assume that `$di` is an instance if the database interface.

```php
$request = $dataInterface->getSql('User/Authenticate');
$result  = $request->execute(['user.login' => 'toto', 'user.password' => 'titi']);
```

> 

See [examples](https://github.com/dbeurive/backend/blob/master/tests/EntryPoints/UnitTests/MySql/Sqls/User/AuthenticateTest.php).




## Calling a procedure from the application

We assume that `$di` is an instance if the database interface.

```php
$procedure = $di->getProcedure('User/Authenticate');
$procedure->setExecutionConfig(['user.login' => 'foo', 'user.password' => 'bar'])
          ->execute();
```

> Please note that you are free to pass any kind of data for `$procedure->setExecutionConfig(...)`.

See [examples](https://github.com/dbeurive/backend/blob/master/tests/EntryPoints/UnitTests/MySql/Procedures/User/AuthenticateTest.php).




