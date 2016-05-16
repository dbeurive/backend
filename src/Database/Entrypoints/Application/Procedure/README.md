# Description

This document describes a procedure.

# Synopsis

## How to create a procedure

The class that implements a procedure must extend the class [`\dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication`](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Procedure/AbstractApplication.php).

Thus, it must provide the following interface:

* `protected function _init(array $inInitConfig=[])`
* `protected function _validateExecutionConfig($inExecutionConfig, &$outErrorMessage)`
* `protected function _execute($inExecutionConfig, AbstractConnector $inConnector)`    
* `public function getDescription()`
    
## _init($inInitConfig=null)

This method is called by the constructor: it initializes the procedure.

Please note that the variable used to configure the procedure comes from the call to the method
[`getProcedure($inName, $inInitConfig=null, $inExecutionConfig=null)`](https://github.com/dbeurive/backend/blob/master/src/Database/DatabaseInterface.php).

A procedure may be complex (*most likely due to a bad design*): some parameters or fields may be mandatory depending on a context of execution.
The value of the parameter `$inInitConfig` may be used to configure procedure, prior to its execution, according to a given context.
    
## _validateExecutionConfig($inExecutionConfig, &$outErrorMessage)

This method is called before the procedure is executed.

The parameter `$inExecutionConfig` contains the execution configuration passed through the call to the method
[`setExecutionConfig($inExecutionConfig)`](https://github.com/dbeurive/backend/blob/master/src/Database/DatabaseInterface.php)
or
[`getProcedure($inName, $inInitConfig=null, $inExecutionConfig=null)`](https://github.com/dbeurive/backend/blob/master/src/Database/DatabaseInterface.php).

## _execute($inExecutionConfig, AbstractConnector $inConnector)

This method executes the procedure.

Note: The second parameter is the database connector that has been introduced during the [database interface's](https://github.com/dbeurive/backend/tree/master/src/Database) initialization.

## getDescription()

This method must return an instance of `\dbeurive\Backend\Database\Entrypoints\Description\Procedure`.

