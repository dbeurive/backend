# Introduction

A procedure presents two levels of application interface:

*  The interface that is common to all API's entry points (see README file for nemaspace `dbeurive\Backend\Database\Entrypoints\Application`).
*  A specific interface. 

Throughout this document, we will describe:

* The specific application interface for procedures.
* The result of a procedure.
* How to create a procedure.

# The specific application interface for procedures.

Procedures' configuration is done using fields' values and parameters. The API used to specify theses values is:

    addInputField($inFieldName, $inFieldValue)
    setInputFields(array $inFields)
    addInputParam($inParamName, $inParamValue)
    setInputParams(array $inParams)

> Please note that the word _field_ refers to the *database' structure*. This is a *table's field*.

However, a procedure may be complex (*most likely due to a bad design*): some parameters or fields may be mandatory depending on a context of execution.
This is the reason why it is possible to specify an arbitrary structure as procedures' configuration.
The method used to set an arbitrary structure as procedures' configuration is:

    setExecutionConfig(array $inExecutionConfig)

Example:

    $dataInterface = DataInterface::getInstance();
    $procedure = $dataInterface->getprocedure('user/Authenticate');
    
    $procedure->addInputField('user.login', $user[0]['user.login'])
              ->addInputField('user.password', $user[0]['user.password'])
              ->execute();
        
    if (! $procedure->isSuccess()) { ... }
                
# Result

In addition to the result's properties described in the general description for the namespace `dbeurive\Backend\Database\Entrypoints\Application`, a procedure's result contains _values_.

* Values are not returned by the RDBMS.
* Values are calculated by the PHP code that implements the procedure, and are not injected in the *data sets* (returned by all API's entry points).
  Please see the definition of a *data set* in the README file for nemaspace `dbeurive\Backend\Database\Entrypoints\Application`.

Specific API:

    __construct($inOptStatus=self::STATUS_NOT_EXECUTED, array $inOptDataSets=[], array $inOptValues=[])
    setValues(array $inValues) 
    getValues()
    isValuesSetEmpty()

Note: please see the definition of a *data set* in the README file for nemaspace `dbeurive\Backend\Database\Entrypoints\Application` to obtain the complete API.

# How to create a procedure

The class that implements procedure request must provide the following interface:

    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage);
    protected function _execute(array $inExecutionConfig, AbstractRdbms $inDbh);
    protected function _init(array $inInitConfig=[]);
    public function getDescription();

## _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage)

This method is called before the procedure is prepared.

The parameter `$inExecutionConfig` contains the execution configuration passed through the call to the method `setExecutionConfig(array $inExecutionConfig)`.

Please not that the value of the parameter `$inExecutionConfig` could be extracted using the method `_getConfig()`:

    protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) {
        $conf = $this->_getConfig();
        // ...    
    }
 
Thus, the parameter `$inExecutionConfig` is not necessary in the method's signature. But we think that it makes things clearer. 

## _execute(array $inExecutionConfig, AbstractRdbms $inDbh);

This method executes the procedure.

> The first parameter `$inExecutionConfig` is not necessary... Please see the documentation for the method `_validateExecutionConfig()`.

The second parameter is the RDBMS handler that has been introduced during the interface's initialization:

    $dsn = "mysql:host=${dbHost};dbname=${dbName};port=${dbPort}";
    $pdo = new \PDO($dsn, $dbUser, $dbPassword);
    $rdbms = new RdbmsHandler();
    $rdbms->setDatabaseHandler($pdo);
        
    $dataInterface = DataInterface::getInstance();
    $dataInterface->setRdbms($rdbms);

## _init(array $inInitConfig=[])

This method is called by the constructor: it initializes the procedure.

Please note that the array used to configure the procedure comes from the call to the method `getProcedure($inName, array $inInitConfig = [], array $inExecutionConfig = null)`.

A procedure may be complex (*most likely due to a bad design*): some parameters or fields may be mandatory depending on a context of execution.
The value of the parameter `$inInitConfig` may be used to configure procedure, prior to its execution, according to a given context.

## getDescription()

This method must return an instance of `\dbeurive\Backend\Database\Entrypoints\Description\Procedure`.



