# Description

This document describes an SQL request.

# Synopsis

## How to create an SQL request

he class that implements a, SQL request must extend the class [`\dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication`](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Sql/AbstractApplication.php).

Thus, it must provide the following interface:

* `protected function _init($inInitConfig=[])`
* `protected function _validateExecutionConfig($inExecutionConfig, &$outErrorMessage)`
* `protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector)`    
* `public function getDescription()`

## _init($inInitConfig=null)

This method is called by the constructor: it initializes the SQL request.

Please note that the variable used to configure the SQL request comes from the call to the method 
[`getSql($inName, $inInitConfig=null, $inExecutionConfig=null)`](https://github.com/dbeurive/backend/blob/master/src/Database/DatabaseInterface.php).
    
## _validateExecutionConfig($inExecutionConfig, &$outErrorMessage)

This method is called before the SQL request is executed.

The parameter `$inExecutionConfig` contains the execution configuration passed through the call to the method
[`setExecutionConfig($inExecutionConfig)`](https://github.com/dbeurive/backend/blob/master/src/Database/DatabaseInterface.php)
or
[`getSql($inName, $inInitConfig=null, $inExecutionConfig=null)`](https://github.com/dbeurive/backend/blob/master/src/Database/DatabaseInterface.php).

## _execute($inExecutionConfig, AbstractConnector $inConnector)

This method executes the SQL request.

Note: The second parameter is the database connector that has been introduced during the [database interface's](https://github.com/dbeurive/backend/tree/master/src/Database) initialization.

## getDescription()

This method must return an instance of `\dbeurive\Backend\Database\Entrypoints\Description\Sql`.

