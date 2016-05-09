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















*  The interface that is common to all API's entry points (see README file for nemaspace `dbeurive\Backend\Database\Entrypoints\Application`).
*  A specific interface. 

Throughout this document, we will describe:

* The specific application interface for SQL requests.
* The result of an SQL request.
* How to create an SQL request.

# The specific application interface for SQL requests

SQL requests' organisations may be complex (with sub selections). Thus, for SQL requests, the configuration's structure is free :
this is an array. Configuration is done through the method `setExecutionConfig(array $inExecutionConfig)`.
    
In order to execute an SQL request, you follow the procedure described below:

* You configure the request for its execution.
* You execute the request.

Specific API:

    setExecutionConfig(array $inExecutionConfig)
    
Example:

    $dataInterface = DataInterface::getInstance();
    $request = $dataInterface->getSql('user/Authenticate');
    
    $request->setExecutionConfig(['user.login' => 'mylogin', 'user.password' => 'mysecret'])
            ->execute();
            
    if ($request->isError()) { ... }
    $result = $request->getResult();
            
Please note that, if you want to reuse the request object for another query, you should reset the configuration.

    $dataInterface = DataInterface::getInstance();
    $request = $dataInterface->getSql('user/Authenticate');
    
    // Use the $request object...
            
    $request->resetExecutionConfig()
            ->setExecutionConfig(['user.login' => 'myPtherLogin', 'user.password' => 'myPtherSecret'])
            ->execute();
       
# Desccription of an SQL request result

The result for an SQL request has no specificity. Please refer to the general description of a result for an API's entry poin
(see README file for nemaspace `dbeurive\Backend\Database\Entrypoints\Application`).

# How to create an SQL request

The class that implements an SQL request must provide the following interface:

    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage);
    protected function _execute(array $inExecutionConfig, AbstractRdbms $inDbh);
    protected function _init(array $inInitConfig=[]);
    public function getDescription();

Note: see `\dbeurive\Backend\Database\Entrypoints\Application\AbstractApplication` and `\dbeurive\Backend\Database\Entrypoints\Application\Sql\AbstractApplication`. 

## _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage)

This method is called before the SQL request is prepared.

The parameter `$inExecutionConfig` contains the execution configuration passed through the call to the method `setExecutionConfig(array $inExecutionConfig)`.

Please not that the value of the parameter `$inExecutionConfig` could be extracted using the method `_getConfig()`:

    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) {
        $conf = $this->_getConfig();
        // ...    
    }
 
Thus, the parameter `$inExecutionConfig` is not necessary in the method's signature. But we think that it makes things clearer. 

## _execute(array $inExecutionConfig, AbstractRdbms $inDbh);

This method executes the SQL request.

> The first parameter `$inExecutionConfig` is not necessary... Please see the documentation for the method `_validateExecutionConfig()`.

The second parameter is the RDBMS handler that has been introduced during the interface's initialization:

    $dsn = "mysql:host=${dbHost};dbname=${dbName};port=${dbPort}";
    $pdo = new \PDO($dsn, $dbUser, $dbPassword);
    $rdbms = new RdbmsHandler();
    $rdbms->setDatabaseHandler($pdo);
        
    $dataInterface = DataInterface::getInstance();
    $dataInterface->setRdbms($rdbms);

## _init(array $inInitConfig=[])

This method is called by the constructor: it initializes the SQL request.

The value is used to create the SQL request, *prior to its execution*. SQL requests' organisations may be complex (with sub selections).
Thus, the construction of the SQL request, *prior to its execution*, may involve configuration parameters.
The value of the parameter `$inInitConfig` contains the necessary parameters for this operation.

Please note that the array used to configure the SQL request comes from the call to the following methods:

 * `\dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication\_getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`
 * `\dbeurive\Backend\Database\Entrypoints\Provider\getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`

## getDescription()

This method must return an instance of `\dbeurive\Backend\Database\Entrypoints\Description\Sql`.



