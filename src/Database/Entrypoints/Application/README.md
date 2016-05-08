# introduction

This document describes the API's entry points from the application's point of view.

* All API's entry points share a common interface.
* The implementations of all API's entry points have the same requirements.
* All API's entry points return a result.

Throughout this document, we will describe:

* The common interface shared by all API's entry points.
* The implementation's requirements.
* The result of an API's entry point.

# The common interface shared by all API's entry points

    execute()
    getResult()
    resetExecutionConfig()
    isSuccess()
    isError()
    isDataSetEmpty()

# The implementation's requirements

All API's entry points must implement the following methods:

    /**
     * Validate the configuration prior to the execution.
     * @param array $inExecutionConfig Configuration pour validate.
     * @param string $outErrorMessage Reference to a string used to store an error message, if an error occurs.
     * @return bool If the execution configuration is valid, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    abstract protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage);

    /**
     * Execute an API's entry point.
     * Please note that the data required for the execution of the entry point can be found within the property.
     * @param array $inExecutionConfig Configuration pour validate.
     * @param AbstractRdbms $inDbh The database handler.
     * @return \dbeurive\Backend\Database\Entrypoints\Application\Sql\Result|\dbeurive\Backend\Database\Entrypoints\Application\Procedure\Result
     */
    abstract protected function _execute(array $inExecutionConfig, AbstractRdbms $inDbh);

    /**
     * Initialize the API's entry point.
     * @param array $inConfig Entry point's configuration.
     */
    abstract protected function _init(array $inConfig=[]);

    /**
     * Return the description of the API's entry point.
     * @return \dbeurive\Backend\Database\Entrypoints\Description\Sql|\dbeurive\Backend\Database\Entrypoints\Description\Procedure
     *         The method returns the description of the API's entry point.
     */
    abstract public function getDescription();

# The result of an API's entry point

Regardless of the type of API's entry point, its result contains:

* An execution' status: not executed yet (`BaseResult::STATUS_NOT_EXECUTED`), successfully executed (`BaseResult::STATUS_SUCCESS`), failure (`BaseResult::STATUS_ERROR`).
* An error message.
* The _data sets_ returned by the API's entry point.
  The term _data set_ represents a set of values (which forms a "row" of values).
  Values in a set (of values) can be:
  * Fields' values returned by the SGBDR.
  * Calculated values returned by the SGBDR.
  * Values calculated by a procedure and injected into the data sets (the rows).

Constructor:

    __construct($inOptStatus=self::STATUS_NOT_EXECUTED, $inOptDataSets=[])

Setters:

    setStatusSuccess()
    setStatusError()
    setDataSets(array $inDataSets)
    setErrorMessage($inErrorMessage)
    
Getters:

    getStatus()
    isError()
    isSuccess()
    hasBeenExecuted()
    getDataSets()
    isDataSetsEmpty()
    getErrorMessage()

Example for a procedure:

    $resultSql = $sql->execute();
    $result = new Result(Result::STATUS_SUCCESS,
        $resultSql->getDataSets(),
        [self::KEY_AUTHENTICATED => ! $resultSql->isDataSetsEmpty()]
    );
        
Example for an SQL request:

    $result = new Result();
    $req = $pdo->prepare($sql);
    if (false === $req->execute($fieldsValues)) {
        $result->setErrorMessage("An error occurred");
        return $result;
    }
    
    $result->setDataSets($req->fetchAll(\PDO::FETCH_ASSOC));
        
# How to create an API's entry point

An API's entry point is represented by a class, within a namespace.

* An API's entry point has a name.
* An API's entry point implements an interface.

Throughout this document, we will describe:

* The name of the API's entry point.
* The interface implemented by the API's entry point.

## The name of the API's entry point

The name of the API's entry point depends on the following parameters:

* The *base namespace* common to all API's entry points of a given category (SQL request or procedure).
* The *absolute namespace* of the class that implements the API's entry point.
* The *base class name* of the class that implements the API's entry point.

### Example for an SQL request

Let's assume that:

* The *base namespace* common to all SQL requests is `dbeurive\BackendTests\Database\Entrypoints\Sql`.
* The *absolute namespace* of the class that implements the SQL request is `dbeurive\BackendTests\Database\Entrypoints\Sql\User`.
* The *base class name* of the class that implements the SQL request is `Authenticate`.

Then, the name of the SQL request is: `user/Authenticate`.

### Example for procedure

Let's assume that:

* The *base namespace* common to all procedures is `dbeurive\BackendTests\Database\Entrypoints\Procedure`.
* The *absolute namespace* of the class that implements the procedure is `dbeurive\BackendTests\Database\Entrypoints\Procedure\User`.
* The *base class name* of the class that implements the procedure is `Authenticate`.

Then, the name of the procedure is: `user/Authenticate`.

## The interface of the API's entry point


The interface is described for all specific API's entry points. Please see the README files for the following namespaces:

* `dbeurive\Backend\Database\Entrypoints\Application\Sql`
* `dbeurive\Backend\Database\Entrypoints\Application\Procedure`
