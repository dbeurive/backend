# Description

This document describes the API's entry points from the application's point of view.



# Synopsis

> Since all the PHP code is documented using PhpDoc annotations, you should be able to exploit the auto completion feature from your favourite IDE.
> If you are using Eclipse, NetBeans or PhPStorm, using this API should be pretty intuitive.

## Using a procedure from within the application's code

```php
$di = DatabaseInterface::getInstance();

/** @var \dbeurive\BackendTest\EntryPoints\Brands\MySql\Procedures\User\Authenticate $procedure */
$procedure = $di->getProcedure('User/Authenticate');

/** @var \dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result $result */
$result = $procedure->addInputField('user.login', 'foo')
                    ->addInputField('user.password', 'bar')
                    ->execute();
                    
if ($result->isSuccess()) {
    $data = $result->getDataSets();
    // ...
} else {
    echo $result->getErrorMessage() ."\n";
    // ...
}
```

See:
* [dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/AbstractApplication.php)
* [dbeurive\backend\Database\Entrypoints\Application\Procedure\AbstractApplication](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Procedure/AbstractApplication.php)

## Using a SQL request from within the application's code

Although it is not recommended, it is however possible. 

```php
$di = DatabaseInterface::getInstance();

/** @var \dbeurive\BackendTest\EntryPoints\Brands\MySql\Sqls\User\Authenticate $request */
$request = $dataInterface->getSql('User/Authenticate');

/** @var \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result $result */
$result = $request->setExecutionConfig(['user.login' => 'foo', 'user.password' => 'bar'])
                  ->execute();

if ($result->isSuccess()) {
    $data = $result->getDataSets();
    // ...
} else {
    echo $result->getErrorMessage() ."\n";
    // ...
}
```

See:
* [dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/AbstractApplication.php)
* [dbeurive\backend\Database\Entrypoints\Application\Sql\AbstractApplication.php](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Sql/AbstractApplication.php)

