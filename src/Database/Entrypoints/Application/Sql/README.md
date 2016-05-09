# Description

This document describes an SQL request.

# Synopsis

## How to create an SQL request

he class that implements a, SQL request must extend the class [`\dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication`](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Sql/AbstractApplication.php).

Thus, it must provide the following interface:

* `protected function _init(array $inInitConfig=[])`
* `protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage)`
* `protected function _execute(array $inExecutionConfig, AbstractLink $inLink)`    
* `public function getDescription()`

## _init(array $inInitConfig=[])

This method is called by the constructor: it initializes the SQL request.

Please note that the array used to configure the SQL request comes from the call to the method [`getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`](https://github.com/dbeurive/backend/blob/master/src/Database/DatabaseInterface.php).
    
## _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage)

This method is called before the SQL request is executed.

The parameter `$inExecutionConfig` contains the execution configuration passed through the call to the method `setExecutionConfig(array $inExecutionConfig)` or `getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`.

## _execute(array $inExecutionConfig, AbstractLink $inLink)`

This method executes the SQL request.

Note: The second parameter is the database link that has been introduced during the [database interface's](https://github.com/dbeurive/backend/tree/master/src/Database) initialization.

## getDescription()

This method must return an instance of `\dbeurive\Backend\Database\Entrypoints\Description\Sql`.



