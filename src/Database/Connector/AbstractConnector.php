<?php

/**
 * This file implements the base class for all "connectors".
 *
 * Connectors are just very thin wrappers around a low-level database handler, such as PDO or mysqli.
 * A connector performs the following actions:
 *   * It exports the configuration's parameters required to initialise the low-level database handler.
 *     See AbstractConnector::getConfigurationParameters
 *   * It initialises the low-level database handler.
 *     See AbstractConnector::connect
 *
 * Please note that you can use PDO to access MySql or SQLite databases.
 * However, the configuration's parameters required to initialise a connexion to a MySql server are not the same than the ones required to open a SQLite database.
 * Please also note that the APIs for PDO and mysqli differ.
 * The purpose of the connectors is to export a unified API for all low-level database handlers.
 *
 * @see AbstractConnector::getConfigurationParameters
 * @see AbstractConnector::connect
 */

namespace dbeurive\Backend\Database\Connector;

/**
 * Class AbstractConnector
 *
 * This class is the base class for all connectors.
 *
 * @package dbeurive\Backend\Database\Connector
 */

abstract class AbstractConnector implements InterfaceConnector
{
    /**
     * @var mixed|null Handler to the database (typically, this is an instance of \PDO).
     */
    private $__databaseHandler=null;
    /**
     * @var array Configuration required to establish the connexion.
     */
    private $__configuration;

    /**
     * This method opens the connection to the database.
     * @param array $inConfiguration Configuration required to open the connection.
     * @return mixed|bool If the connexion is successfully established, then the method returns handler to the database.
     *         This can be an instance of \PDO, for example.
     *         Otherwise, the method throws an exception.
     * @throws \Exception
     */
    abstract protected function _connect(array $inConfiguration);
    
    /**
     * Create a connector.
     * @param array $inOptConfiguration Configuration's parameters required by the database handler to establish a connexion.
     * @param bool $inOtpConnect This flag specifies whether a connexion ro the database must be established or not.
     * @throws \Exception
     */
    final public function __construct(array $inOptConfiguration, $inOtpConnect=false) {
        $this->__configuration = $inOptConfiguration;
        if ($inOtpConnect) {
            $this->connect();
        }
    }

    /**
     * Open the connection to the database and returns the handler rto the database.
     * @param array $inConfiguration Configuration required to open the connection.
     * @return mixed The handler to the database.
     * @throws \Exception
     */
    public function connect() {
        $this->__databaseHandler = $this->_connect($this->__configuration);
        return $this->__databaseHandler;
    }

    /**
     * Return the handler to the database.
     * @return mixed The handler to the database.
     * @throws \Exception
     */
    public function getDatabaseHandler() {
        if (is_null($this->__databaseHandler)) {
            throw new \Exception("You did not open any connection to the database!");
        }
        return $this->__databaseHandler;
    }

    /**
     * Return the configuration for the handler to the database.
     * @return array The configuration.
     * @throws \Exception
     */
    public function getConfiguration() {
        return $this->__configuration;
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

