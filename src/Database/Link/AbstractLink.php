<?php

/**
 * This file implements the base class for all "database links".
 */

namespace dbeurive\Backend\Database\Link;

/**
 * Class AbstractLink
 *
 * This class is the base class for all "database links".
 *
 * @package dbeurive\Backend\Database\Link
 */

abstract class AbstractLink
{
    /**
     * Name for an option.
     */
    const OPTION_NAME = 'name';
    /**
     * Description of an option.
     */
    const OPTION_DESCRIPTION = 'description';

    /**
     * @var null|array Configuration for the "database link".
     */
    private $__configuration = null;
    /**
     * @var mixed Database connexion handler (it can be an instance of \PDO, for example).
     */
    protected $_databaseConnexionHandler = null;
    /**
     * @var null|int Error code.
     */
    private $__errorCode = null;
    /**
     * @var null|string Error message.
     */
    private $__errorMessage = null;

    // -----------------------------------------------------------------------------------------------------------------
    // Abstract methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Create an instance of a "database link".
     */
    abstract public function __construct();

    /**
     * This method returns the list of configuration's options required for this "database link".
     * @return array|false If the method returns the value false, then it means that there is no need to return the list of options.
     *         Otherwise, the method returns an array.
     *         Each element of the returned array is an associative array that presents to entries.
     *         * \dbeurive\Backend\Database\Link\AbstractLink::OPTION_NAME ('name'): the name of the option.
     *         * \dbeurive\Backend\Database\Link\AbstractLink::OPTION_DESCRIPTION ('description'): the description of the option.
     *
     * @see \dbeurive\Backend\Database\Link\AbstractLink::OPTION_NAME
     * @see \dbeurive\Backend\Database\Link\AbstractLink::OPTION_DESCRIPTION
     */
    abstract public function getConfigurationOptions();

    /**
     * Quote a value.
     * Typically: if `$pdo` is an instance of `\PDO`, this method should return `$pdo->quote($inValue)`.
     * @param string $inValue Value to quote.
     * @return string The method returns the quoted value.
     */
    abstract public function quoteValue($inValue);

    /**
     * Quote a field's name.
     * @param string $inFieldName Name of the field to quote.
     * @return string The method returns the quoted field.
     */
    abstract public function quoteFieldName($inFieldName);

    /**
     * Return the schema of the database.
     * @return array|false If the operation is successful, then the method returns an array that represents the schema of the database:
     *                     array(   <table name> => array(<field name>, <field name>...),
     *                              <table name> => array(<field name>, <field name>...),
     *                              ...)
     *                     Otherwise, the method returns the value false.
     *                     If an error occurred, then you can use the method `getErrorMessage()` to get information about the problem.
     * @see getErrorMessage
     */
    abstract public function getDatabaseSchema();

    /**
     * This method checks a given configuration.
     * @param array $inConfiguration The given configuration.
     * @return array The method returns an array that represents a list of error messages.
     */
    abstract protected function _checkConfiguration(array $inConfiguration);

    /**
     * This method opens the connection to the database.
     * @param array $inConfiguration Configuration required to open the connection.
     * @return bool If the connexion is successfully established, then the method returns the value true.
     *         Otherwise, it returns the value false.
     */
    abstract protected function _connect(array $inConfiguration);

    // -----------------------------------------------------------------------------------------------------------------
    // Protected API
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set an error code.
     * @param int $inCode Error code to set.
     */
    protected function _setErrorCode($inCode) {
        $this->__errorCode = $inCode;
    }

    /**
     * Set an error message.
     * @param string $inMessage Error message to set.
     */
    protected function _setErrorMessage($inMessage) {
        $this->__errorMessage = $inMessage;
    }

    /**
     * Get the database connexion handler.
     * For example, the returned value may be an instance of \PDO.
     * @return mixed The method returns the database connexion handler.
     */
    protected function _getDatabaseConnexionHandler() {
        return $this->_databaseConnexionHandler;
    }

    /**
     * Return the configuration for this "database link".
     * @return array|null The method returns the configuration for this "database link", or null if no configuration is specified.
     */
    protected function _getConfiguration() {
        return $this->__configuration;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Public API
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Open a connexion to the database.
     * @return bool If the connexion was successfully established, then the method returns the value true.
     *         Otherwise, it returns the value false.
     * @throws \Exception
     */
    public function connect() {
        if (! is_null($this->__configuration)) {
            return $this->_connect($this->__configuration);
        }
        throw new \Exception("You did not configure the database link prior to the connexion.");
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the database connexion handler.
     * This method can be used in replacement for the method "connect()".
     * If you already have an open connection to the database (through a database connexion handler), then you can use it as database connexion handler for this "database link".
     * @param mixed $inDatabaseHandler Database connexion handler. Typically: an instance of \PDO.
     * @see connect
     */
    public function setDatabaseConnexionHandler($inDatabaseHandler) {
        $this->_databaseConnexionHandler = $inDatabaseHandler;
    }

    /**
     * Set the configuration for this "database link".
     * @param $inConfiguration
     * @return array If the given configuration is valid, then the method returns an empty array.
     *         Otherwise, it returns a list of error messages.
     */
    public function setConfiguration($inConfiguration) {
        $errors = $this->_checkConfiguration($inConfiguration);
        if (count($errors) > 0) {
            return $errors;
        }
        $this->__configuration = $inConfiguration;
        return [];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the database connexion handler.
     * @return mixed This method returns the database connexion handler.
     */
    public function getDatabaseConnexionHandler() {
        return $this->_databaseConnexionHandler;
    }

    /**
     * Return the code of the last error.
     * @return int|null The method returns the code of the last error.
     */
    public function getErrorCode() {
        return $this->__errorCode;
    }

    /**
     * Return the message that describes the last error.
     * @return null|string The method returns the message of the last error.
     */
    public function getErrorMessage() {
        return $this->__errorMessage;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Statics
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the fully qualified name of the object's class.
     * Please not that this method should return the value of __CLASS_.
     * @return string The fully qualified name of the element's class.
     */
    public static function getFullyQualifiedClassName() {
        $reflector = new \ReflectionClass(get_called_class());
        return $reflector->getName();
    }

}