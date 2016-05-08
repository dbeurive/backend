# introduction

This document describes the *database' API's entry points*, called *API's entry points* in an abbreviated form.  

The *API's entry points* implements the *database's access layer*.

This layer consists of two sub-layers:

* The first sub-layer consists of a set of classes that implements SQL requests.
  We call this sub-layer the *SQL layer*. API's entry points that are part of the SQL layer are called *SQL requests*.  
* The second sub-layer consists of a set of classes that implements procedures used to access data.
  We call this sub-layer the *procedure layer*. API's entry points that are part of the procedure layer are called *procedures*.

> Please note that the *procedure layer* accesses the database through the *SQL layer*.
  And the application accesses the database through the *procedure layer*.

Each layer presents two interfaces :

* An interface used to access the database through the layer. This interface is implemented within the namespace `dbeurive\Backend\Database\Entrypoints\Application`.
* An interface used to describe the layer. This interface is implemented within the namespace `dbeurive\Backend\Database\Entrypoints\Description`. 





# User's interface: how to use the procedures

This section presents the database's access layers from the user's point of view.
The user should access the database within the controllers, through the procedure layer.
In this section, we do not cover the creation of procedures. This point will be discussed later.

## Procedure's interface

### General presentation

Procedures are configured through two kinds of values :
* Database's fields (called *input fields*).
* Input parameters.

Please note that a procedure's configuration should not be complex.
However, in practice it could be (*most likely due to a bad design*): some parameters or (database) input fields may be mandatory depending on a context of execution.
Therefore, the API does not require to declare fields or parameters as mandatory.
Furthermore, you may need to set up an extremely complex configuration that cannot be expressed via a set of input fields and parameters. To do that, you can use the method `setExecutionConfig()`.

> The methods `addInputField()`, `setInputFields()`, `addInputParam()` and `setInputParams()` should cover any kind of configuration.
  However, for extremely complex configuration, it is always possible to use the *generic configuration's holder* through the method `setSpecialExecutionConfig()`.

All procedures return an instance of `\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result`.

### Procedures' API

    \dbeurive\Backend\Database\Entrypoints\Provider::getProcedure($inName, array $inInitConfig = [], array $inExecutionConfig = null)
    public function addInputField($inFieldName, $inFieldValue)
    public function setInputFields(array $inFields)
    public function addInputParam($inParamName, $inParamValue)
    public function setInputParams(array $inParams)
    public function setSpecialExecutionConfig(array $inExecutionConfig)
    public function resetExecutionConfig()
    public function execute()

**Get a procedure**

	\dbeurive\Backend\Database\Entrypoints\Provider::getProcedure($inName, array $inInitConfig = [], array $inExecutionConfig = null)

**Set input fields**

    public function setInputFields(array $inFields)
    public function addInputParam($inParamName, $inParamValue)

> Please note that the use of the methods `addInputField()` and `setInputFields()` gives the developer access to the following convenient method:
  `_checkMandatoryInputFields()`. This method is used within the class that implements the procedure.
  Method `_checkMandatoryInputFields()` is used to check the presence of the mandatory input fields against the procedure's description.   

**Set input parameters**

    public function addInputParam($inParamName, $inParamValue)
    public function setInputParams(array $inParams)

> Please note that the use of the methods `addInputParam()` and `setInputParams()` gives the developer access to the two following convenient method:
  `_checkMandatoryInputParams()`. This method is used within the class that implements the procedure.
  Method `_checkMandatoryInputParams()` is used to check the presence of the mandatory parameters against the procedure's description.  

**Apply a specific configuration - optionally**

    public function setSpecialExecutionConfig(array $inExecutionConfig)

> The methods `addInputField()`, `setInputFields()`, `addInputParam()` and `setInputParams()` should cover any kind of configuration.
  However, for extremely complex configuration, it is always possible to use the *generic configuration's holder* through the method `setSpecialExecutionConfig()`.

**Reset all configuration**

If you want to reuse the same instance of a procedure to perform a new treatment, you must reset the previously set configuration.

    public function resetExecutionConfig()
    
**Execute the procedure**

    public function execute()

**Exploit the result of a procedure**

The method `execute()` returns an instance of the class `\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result`. The following methods are available:

* `reset()`
* `getStatus()`: return one of the following values: `STATUS_NOT_EXECUTED`, `STATUS_SUCCESS` or `STATUS_ERROR` (defined within `\dbeurive\Backend\Database\Entrypoints\Application\BaseResult`).
* `isError()`
* `isSuccess()`
* `hasBeenExecuted()`
* `isDataSetEmpty()`
* `isValuesSetEmpty()`
* `getErrorMessage()`
* `getData()`: get the *data sets* selected by the API's entry point (the procedure).
  Please note that the term *data set* represents a set of data (which forms a *row*) extracted from the database.
  A *row* of data may contain fields' values and calculated values.
  All *data sets* (all *rows*) are returned **by the RDBMS** to the PHP client.
  Data sets may contain tables' fields's values, or values calculated by the RDBMS. 
* `getValues()`: get the list of values calculated by the API's entry point.
  Please note that the term *value* represents a data that has been calculated **using PHP code**.
  A *value* is **NOT** computed by the RDBMS.



# Developer's interface

Developers create SQL requests and procedures. This section presents the following topics:

* The generic rules that apply to the creation of SQL requests and procedures.
* The generic workflow for all entry points.
* The creation of SQL requests.
* The creation of procedures.
* The API for using the SQL requests (within the procedures).

## The generic rules that apply to the creation of SQL requests and procedures

All API's entry points (SQL requests or procedures) must implement the following methods :

    abstract protected function _init(array $inConfig=[])
    abstract protected function _validateExecutionConfig(&$outErrorMessage)
    abstract protected function _execute(\dbeurive\Backend\Database\RDBMSHandler $inDbh)
    abstract public function getDescription()
     
### _init
     
The method `_init(array $inInitConfig=[])` is called to initialize the entry point during its creation, prior to its execution.
The method `_init(array $inInitConfig=[])` can perform any action on the entry point. For example, this method can take an SQL template and produce an SQL request according to the context (expressed via the given configuration `$inInitConfig`).

> Please note that the entry point is created through the call to one of the following methods:
  * **For procedures**: `dbeurive\Backend\Database\Entrypoints\Provider::getProcedure($inName, array $inInitConfig = [], array $inExecutionConfig = null)`
  * **For SQL request**: `dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication::_getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`
  The value of `$inInitConfig` is passed to the method `_init()`.

### _validateExecutionConfig

The method `_validateExecutionConfig()` is called before the entry point is executed (that is, before the call to the method `_execute()`).
This method should check the given configuration for the execution.

> Please note that the configuration for the execution is given through one of the following calls:
  * **For procedures**: `dbeurive\Backend\Database\Entrypoints\Provider::getProcedure($inName, array $inInitConfig = [], array $inExecutionConfig = null)`
  * **For SQL request**: `dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication::_getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`
  * `setExecutionConfig(array $inExecutionConfig)`

<!-- -->

> Please note that the method `setExecutionConfig()` can be used to set, or modify, the execution's configuration of the entry point after it was created. 

### _execute
     
The method `_execute()` executes the entry point.

This method must return an instance of the following class:
*  **For SQL requests**: `\dbeurive\Backend\Database\Entrypoints\sql\application\Result`
*  **For procedures**: `\dbeurive\Backend\Database\Entrypoints\procedure\application\Result`

### getDescription

This method describes the entry point. Information set within this method is used to create the documentation.

## The generic workflow for all entry points

First, you create an instance of a given API's entry point.

* If you are within a procedure and want to create an instance of an SQL request, you call the following method:
  `\dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication::_getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`
* If you are outside the database's access layer and want to create an instance of a procedure, you call the following method:
  `\dbeurive\Backend\Database\Entrypoints\Provider::getProcedure($inName, array $inInitConfig = [], array $inExecutionConfig = null)`

> Please note that you can create an instance of an SQL request from outside the database's access layer, by using the following method:
  `\dbeurive\Backend\Database\Entrypoints\Provider::getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)`.
  However, you should not call this method since SQL requests should not be executed from outside a procedure.
  This method is used for testing purposes.

Once the API's entry point has been created, you may want to execute it.

The way to do that is pretty simple:
* First, you may set the configuration for the execution.
 * For SQL requests: using the method `setExecutionConfig()`. Please note that this action may have already been done (through the call to `\dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractApplication::_getSql(...)`).
 * For procedures: using the methods `addInputField()`/`setInputFields()`, `addInputParam()`/`setInputParams` or `setExecutionConfig()`. Please note that this action may have already been done (through the call to `Provider::getProcedure(...)`).
* Then you trigger the execution using the method `execute()`.
   Prior to any other action, the method `execute()` will call the method `_validateExecutionConfig()`.
   `_validateExecutionConfig()` will check that the configuration for the execution is valid.
   Then, if the configuration for the execution is valid, it will call the method `_execute(RDBMSHandler $inDbh)`.

The method `execute()` will return the result. It can be an instance of the following classes:
*  **For SQL requests**: `\dbeurive\Backend\Database\Entrypoints\Application\Sql\Result`
*  **For procedures**: `\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result`

## The creation of SQL requests

Classes that implement SQL requests may use the following methods:

    protected function _setSql($inSql)
    protected function _getSql()

* The method `_setSql()` may be used to save an SQL request after it was initialized (within the method `_init()`).
* The method `_getSql()` may be used within the method `_execute()` to get the previously initialized SQL request.

Please note that using these methods is not an obligation. 

## The creation of procedures.

Classes that implement procedures may use the following methods:   
     
    protected function _getInputFields()
    protected function _getInputParams()
    protected function _getSql($inName, array $inInitConfig = [], array $inExecConfig = null)
    protected function _checkMandatoryInputFields()
    protected function _checkMandatoryInputParams()

> These methods should be called within the method `_execute()`.
     
The method `_getInputFields()` can be used to get the list of fields given through the call to `addInputField()` or `setInputFields()`.

The method `_getInputParams()` can be used to get the list of parameters given through the call to `addInputParam()` or `setInputParams()`.

The method `_getSql()` should be used to get an instance of an SQL request.

The method `_checkMandatoryInputFields()` can be used to check the presence of the declared mandatory fields (via the method `getDescription()`).

The method `_checkMandatoryInputParams()` can be used to check the presence of the declared mandatory parameters (via the method `getDescription()`).

> Please note that a procedure's configuration should not be complex.
  However, in practice it could be (*most likely due to a bad design*) : some fields may be mandatory depending on a context of execution.
  Therefore, methods `_checkMandatoryInputFields()` and `_checkMandatoryInputParams()` are not automatically called before the procedure's execution.
  These methods are provided because they are convenient ways to check the execution's configuration against the procedure's description, **if the procedure's configuration does not depend upon the context**.

## The API for using the SQL requests within the procedures

    \dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractEntryPoint::_getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)
    public function setExecutionConfig(array $inExecutionConfig)
    public function resetExecutionConfig()
    public function execute()

> Please note that SQL requests' organisations may be complex (with sub selections).
  Thus, for SQL requests, the configuration's structure is free.
  Configuration for SQL requests is done using the *generic configuration's holder* through the method `setExecutionConfig()`.

### Get an SQL request

    \dbeurive\Backend\Database\Entrypoints\Application\Procedure\AbstractEntryPoint::_getSql($inName, array $inInitConfig = [], array $inExecutionConfig = null)
    
### If necessary, reset the request's configuration

	public function resetExecutionConfig()
	
> You need to call this method if you already used the instance of the SQL request.

### Configure the SQL request before its execution

    public function setExecutionConfig(array $inExecutionConfig)

### Execute the SQL request

	public function execute()

### Exploit the result from the SQL request's execution

The method execute() returns an instance of the class \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result`. The following methods are available:

* `reset()`
* `getStatus()`: return one of the following values: `STATUS_NOT_EXECUTED`, `STATUS_SUCCESS` or `STATUS_ERROR` (defined within `\dbeurive\Backend\Database\Entrypoints\Application\BaseResult`).
* `isError()`
* `isSuccess()`
* `hasBeenExecuted()`
* `isDataSetEmpty()`
* `getErrorMessage()`
* `getData()`: get the *data sets* selected by the API's entry point (The SQL request, in this particular case).
  Please note that the term *data set* represents a set of data (which forms a *row*) extracted from the database.
  A *row* of data may contain fields' values and calculated values.
  All *data sets* (all *rows*) are **returned by the RDBMS** to the PHP client.






















