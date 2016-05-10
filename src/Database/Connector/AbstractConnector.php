<?php

namespace dbeurive\Backend\Database\Connector;

abstract class AbstractConnector implements InterfaceConnector
{
    const OPTION_NAME = 'name';
    const OPTION_DESCRIPTION = 'description';

    /**
     * @var mixed|null Handler to the database (typically, this is an instance of \PDO).
     */
    private $__databaseHandler=null;
    /**
     * @var array Configuration required to establish the connexion.
     */
    private $__configuration;

    /**
     * This method returns the list of configuration's options required for this "database connector".
     * @return array|false If the method returns the value false, then it means that there is no need to return the list of options.
     *         Otherwise, the method returns an array.
     *         Each element of the returned array is an associative array that presents to entries.
     *         * \dbeurive\Backend\Database\Connector\AbstractConnector::OPTION_NAME: the name of the option.
     *         * \dbeurive\Backend\Database\Connector\AbstractConnector::OPTION_DESCRIPTION: the description of the option.
     *
     * @see \dbeurive\Backend\Database\Connector\AbstractConnector::OPTION_NAME
     * @see \dbeurive\Backend\Database\Connector\AbstractConnector::OPTION_DESCRIPTION
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

