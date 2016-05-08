# Introduction

This document describes the database's interface.

The database is accessed through PHP procedures. You can think about traditional databases' stored procedures that act as an access layer for data management, except that we implement this access layer using the PHP language.   

Therefore, instead of being embedded within the database, the access layer is implemented within the database's client. This design has a major drawback: data management is slower. However, PHP implementation on the database's access layer is much easier to implement an maintain.

# Environment configuration

The database's access layer is a collection of (PHP) procedures that access the database through SQL requests.
SQL requests and procedures are PHP classes that are organized according to namespaces that match directory trees (following the PSR-4 specification).  

> All SQL requests are PHP classes that share the same *base namespace* and *base directory*.
  All procedures are PHP classes share the same *base namespace* and *base directory*.
  Whether we are talking about SQL requests or procedures, the organization of the associated PHP classes must follow the **PSR4 specification**.

The database's interface gains access to the database through a provided database's handler, which is an instance of the class `\dbeurive\Backend\Database\AbstractRdbms`.

The database's interface provides the database's client with a set of functionaries that use information from the database's schema.
In order to avoid database' schema's discovery on each clients' executions, information about the database' schema is stored in a PHP file that is created once, using a provided script.
Please note that this script should be used each time you modify the structure of the database.   
 
To sum up, the configuration's requirements consist of:

*  The path to the base directory used to store all SQL requests.
*  The base namespace for all SQL requests.
*  The path to the base directory used to store all procedures.
*  The base name space for all procedures.
*  A database handler, which is an instance of the class `\dbeurive\Backend\Database\AbstractRdbms`.
*  The path to the previously generated PHP file that contains the database' schema.

Sample code:

    // Create database handler (which is an instance of the class "AbstractRdbms").
    
    $dsn = "mysql:host=${dbHost};dbname=${dbName};port=${dbPort}";
    $pdo = new \PDO($dsn, $dbUser, $dbPassword);
    $rdbms = new RdbmsHandler();
    $rdbms->setDatabaseHandler($pdo);

    // Configure the database' interface.

    $dataInterface = DataInterface::getInstance(); // <=> DataInterface::getInstance('default')
    $dataInterface->setSqlRepositoryBasePath($sqlRepositoryPath);
    $dataInterface->setSqlBaseNameSpace($sqlBaseNamespace);
    $dataInterface->setProcedureRepositoryBasePath($procedureRepositoryPath);
    $dataInterface->setProcedureBaseNameSpace($procedureBaseNamespace);
    $dataInterface->setPhpDatabaseRepresentationPath($phpDatabaseRepPath);
    $dataInterface->setRdbms($rdbms);

Please note that you can create more than one database's interface (to one or more databases). For example:

    $operational = DataInterface::getInstance(); // <=> DataInterface::getInstance('default');
    $dataware    = DataInterface::getInstance('dataware');

Later in your code, you can get the previously created instance :

    // Get the database interface "default" (that has already been created).
    $operational = DataInterface::getInstance(); // <=> $dataInterface = DataInterface::getInstance('dataware');
    
    // Get the database interface "dataware" (that has already been created).
    $dataware = DataInterface::getInstance('dataware');

# SQL requests

## Typical example

Hypothesis:

* **File**: `/Users/denisbeurive/Desktop/projet/web/dbeurive/Backend/tests/database/entrypoints/sql/user`
* **Namespase**: `dbeurive\BackendTests\Database\Entrypoints\Sql\User`

Somewhere during the initialization' sequence:

    $sqlRepositoryPath = '/Users/denisbeurive/Desktop/projet/web/dbeurive/Backend/tests/database/entrypoints/sql':
    $sqlBaseNamespace = 'dbeurive\BackendTests\Database\Entrypoints\Sql';
    
    $dataInterface->setSqlRepositoryBasePath($sqlRepositoryPath);
    $dataInterface->setSqlBaseNameSpace($sqlBaseNamespace);
    
The SQL request:

    // File: /Users/denisbeurive/Desktop/projet/web/dbeurive/Backend/tests/database/entrypoints/sql/user/Authenticate.php
    namespace dbeurive\BackendTests\Database\Entrypoints\Sql\User;
    
	class Authenticate extends AbstractEntryPoint
	{
	    private static $__conditionFields = ['user.login', 'user.password'];
	    private static $__sql = "SELECT __USERS__
	                             FROM   user
	                             WHERE  `user`.`login`=?
	                               AND  `user`.`password`=?";

	    // -----------------------------------------------------------------------------------------------------------------
	    // Mandatory methods.
	    // -----------------------------------------------------------------------------------------------------------------

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\sql\AbstractEntryPoint
	     */
	    public function _init(array $inConfig=[]) {
	        $this->_setSql(self::__getSql());
	    }

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\sql\AbstractEntryPoint
	     */
	    protected function _validateExecutionConfig(&$outErrorMessage) {
	        // Make sure that we have all the fields used within the clause "WHERE" of the SQL request.
	        if (! ArrayUtilities::hasKeys(self::$__conditionFields, $this->_execConfig)) {
	            $outErrorMessage = "Invalid SQL configuration. Mandatory fields are: " . implode(', ', self::$__conditionFields) . "\nSee: " . __FILE__;
	            return false;
	        }
	        return true;
	    }

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\sql\AbstractEntryPoint
	     */
	    protected function _execute(AbstractRdbms $inRdbmsHandler) {
	        /* @var \PDO $pdo */
	        $pdo = $inRdbmsHandler->getDatabaseHandler();

	        $result = new Result();
	        $fieldsValues = ArrayUtilities::keepOnly(self::$__conditionFields, $this->_execConfig, true);
	        $req = $pdo->prepare($this->_getSql());
	        if (false === $req->execute($fieldsValues)) {
	            $message = "SQL request failed:\n" .
	                StringUtilities::linearize($this->_getSql()) . "\n" .
	                "Condition fields: " .
	                implode(', ', self::$__conditionFields) . "\n" .
	                "Bound to values: " .
	                implode(', ', $fieldsValues);
	            $result->setErrorMessage($message);
	            return $result;
	        }

	        $result->setData($req->fetchAll(\PDO::FETCH_ASSOC));
	        return $result;
	    }

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\sql\AbstractEntryPoint
	     */
	    public function getDescription() {

	        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
	        $doc->setDescription('This request checks that the authentication data is valid.')
	            ->addTags(Tags::AUTHENTICATION)
	            ->addOutputValue(OutputValue::OUTPUT_VALUE_IS_AUTHENTICATED, 'This value indicates whether the user is authenticated or not.')
	            ->addEntityActions(Entities::USER, Actions::SELECT)
	            ->setType($doc::TYPE_SELECT)
	            ->setSql($this->__getSql())
	            ->addTable('user')
	            ->setSelectedFields(['user.*'])
	            ->setConditionFields(self::$__conditionFields);
	        return $doc;
	    }

	    // -----------------------------------------------------------------------------------------------------------------
	    // Private methods.
	    // -----------------------------------------------------------------------------------------------------------------

	    /**
	     * Create the SQL request from the request's template.
	     * @return string The method returns a string that represents the SQL request.
	     */
	    private function __getSql() {
	        return preg_replace('/__USERS__/', $this->getTableFieldsNames('user', self::FIELDS_FULLY_QUALIFIED_AS_SQL), self::$__sql);
	    }
	}

# Procedures

Hypothesis:

**File**: `/Users/denisbeurive/Desktop/projet/web/dbeurive/Backend/tests/database/entrypoints/procedure/user/Authenticate.php`
**Namespace**: `dbeurive\BackendTests\Database\Entrypoints\Sql\Profile`

Somewhere during the initialization' sequence:

    $procedureRepositoryPath = '/Users/denisbeurive/Desktop/projet/web/dbeurive/Backend/tests/database/entrypoints/procedure':
    $procedureBaseNamespace = 'dbeurive\BackendTests\Database\Entrypoints\Procedure';
    
    $dataInterface->setProcedureRepositoryBasePath($procedureRepositoryPath);
    $dataInterface->setProcedureBaseNameSpace($procedureBaseNamespace);

The procedure

    // File: /Users/denisbeurive/Desktop/projet/web/dbeurive/Backend/tests/database/entrypoints/procedure/user/Authenticate.php
	namespace dbeurive\BackendTests\Database\Entrypoints\Procedure\User;

	class Authenticate extends AbstractEntryPoint {

	    const SQL_AUTHENTICATE  = 'user/Authenticate';
	    const KEY_AUTHENTICATED = 'authorized';

	    static private $__mandatoryFields = [['user.login'], ['user.password']];

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
	     */
	    public function _init(array $inConfig=[]) {
	    }

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\procedure\AbstractApi.
	     */
	    protected function _validateExecutionConfig(&$outErrorMessage) {
	        $outErrorMessage = null;
	        if (false === $this->_checkMandatoryInputFields()) {
	            $outErrorMessage = "Some mandatory fields are missing. Mandatory fields are: " . implode(', ', self::$__mandatoryFields) . "\nSee: " . __FILE__;
	        }
	        return true;
	    }

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\procedure\AbstractApi.
	     */
	    protected function _execute(AbstractRdbms $inRdbmsHandler) {
	        $sql = $this->_getSql(self::SQL_AUTHENTICATE, [], $this->_getInputFields());
	        $resultSql = $sql->execute();
	        $result = new \dbeurive\Backend\Database\Entrypoints\procedure\Result(Result::STATUS_SUCCESS,
	            $resultSql->getData(),
	            [self::KEY_AUTHENTICATED => ! $resultSql->isDataSetEmpty()]
	        );
	        return $result;
	    }

	    /**
	     * Check if the user is authenticated.
	     * @return bool If the user is authenticated, then the method returns the value true.
	     *         Otherwise, it returns the value false.
	     */
	    public function isAuthorized() {
	        return $this->getResult()->getValues()[self::KEY_AUTHENTICATED];
	    }

	    /**
	     * If the user is authenticated, then the method returns the list of fields that describes the user.
	     * @return array The method returns the list of fields that describes the user.
	     */
	    public function getUserData() {
	        return $this->getResult()->getData();
	    }

	    // -----------------------------------------------------------------------------------------------------------------
	    // Mandatory static methods.
	    // -----------------------------------------------------------------------------------------------------------------

	    /**
	     * @see \dbeurive\Backend\Database\Entrypoints\InterfaceApi
	     */
	    public function getDescription() {
	        $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Procedure();
	        $doc->setDescription("This procedure is used to authenticate a user based on a provided set of login and password.")
	            ->setRequests([self::SQL_AUTHENTICATE])
	            ->addTags(Tags::AUTHENTICATION)
	            ->setMandatoryInputFields(self::$__mandatoryFields)
	            ->addOutputField('user.*')
	            ->addOutputValue('authorized', 'This flag indicates whether the user has been successfully authenticated or not. TRUE: authentication succeed, FALSE: authentication failed.')
	            ->addEntityActions(Entities::USER, Actions::AUTHENTICATE);

	        return $doc;
	    }
	}

    
    