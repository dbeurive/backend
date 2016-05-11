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
class Authenticate extends AbstractApplication
{
    // Initialize the request.
    public function _init(array $inInitConfig=[]) { /* ... */ }

    // Validate the configuration for the execution of the request (return true if OK, false otherwise).
    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) { /* ... */ }

    // Execute request.
    protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector) { /* ... */ } 

    // Document the request.
    public function getDescription() {
        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();

        /* ... */

        return $doc;
    }
```

See:

* [API common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/AbstractEntryPoint.php)
* [API for an SQL request](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Sql/AbstractApplication.php)
* [Elements of documentation common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/AbstractDescription.php)
* [Elements of documentation specific to SQL requests](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/Sql.php)
* [Examples of SQL requests](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Sqls)




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
class Authenticate extends AbstractApplication {

    const SQL_AUTHENTICATE  = 'User/Authenticate';

    // Initialize the procedure.
    public function _init(array $inInitConfig=[]) { /* ... */ }

    // Validate the execution parameters, prior to the procedures' execution (return true if OK, false otherwise).
    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) { /* ... */ }

    // Execute the procedure.
    protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector) { /* ... */ }

    // Document the procedure/
    public function getDescription() {
        $doc = new Description\Procedure();

        /* ... */

        return $doc;
    }
}
```

* [API common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/AbstractEntryPoint.php)
* [API for a procedure](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Procedure/AbstractApplication.php)
* [Elements of documentation common to SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/AbstractDescription.php)
* [Elements of documentation specific to procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/Procedure.php)
* [Examples of procedures](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Procedures/User)





## Calling an SQL request from a procedure

```php
use dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result;

protected function _execute(array $inExecutionConfig, AbstractLink $inLink) {

    // Get an instance of the SQL request.
    $sql = $this->_getSql('User/Authenticate', [], $this->_getInputFields());
    
    // Execute the SQL request.
    $resultSql = $sql->execute();
    
    // Return the result to the application.
    $result = new Result(Result::STATUS_SUCCESS,
        $resultSql->getDataSets();
    );
    return $result;
}
```

See [examples](https://github.com/dbeurive/backend/tree/master/tests/EntryPoints/Brands/MySql/Procedures/User)




## Calling an SQL request from the application

We assume that `$di` is an instance if the database interface.

```php
$request = $di->getSql('User/Authenticate');
$result = $request->setExecutionConfig(['user.login' => 'foo', 'user.password' => 'bar'])
                  ->execute();
```

See [examples](https://github.com/dbeurive/backend/tree/master/tests/UnitTests/MySql/Sqls/User).




## Calling a procedure from the application

We assume that `$di` is an instance if the database interface.

```php
$procedure = $di->getProcedure('User/Authenticate');
$procedure->addInputField('user.login', 'foo')
          ->addInputField('user.password', 'bar')
          ->execute();
```

See [examples](https://github.com/dbeurive/backend/tree/master/tests/UnitTests/MySql/Procedures/User).




