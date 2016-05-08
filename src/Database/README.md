# Description

This directory contains the implementation on the "database interface".

The "database interface" provides:
 
* Access to the database (through the API's entry points).
  Through this interface the application gains access to API's entry points (SQL requests and procedures).
* Information about the database.
  For example, an API's entry point may request the list of fields that compose a given table.
     
# Synopsis

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
