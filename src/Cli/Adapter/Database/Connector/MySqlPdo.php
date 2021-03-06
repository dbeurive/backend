<?php

/**
 * This file implements the "connector" for MySql, using PDO as database handler.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\Connector;

use dbeurive\Input\Specification;
use dbeurive\Input\SpecificationsSet;

/**
 * Class MySqlPdo
 *
 * This class implements the "connector" for MySql, using PDO as database handler.
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\Connector
 */

class MySqlPdo extends AbstractConnector
{
    /**
     * This constant defines the name of the parameter used to specify the name, or the IP address, of the host that runs the database' server.
     */
    const DB_HOST = 'db-host';
    /**
     * This constant defines the name of the parameter used to specify the login used to access the database' server.
     */
    const DB_USER = 'db-user';
    /**
     * This constant defines the name of the parameter used to specify the password used to access the database' server.
     */
    const DB_PASSWORD = 'db-password';
    /**
     * This constant defines the name of the parameter used to specify the port number used by the server to listen to incoming requests.
     */
    const DB_PORT = 'db-port';
    /**
     * This constant defines the name of the parameter used to specify the name of the database.
     */
    const DB_NAME = 'db-name';

    /**
     * {@inheritdoc}
     * @see InterfaceConnector
     */
    static public function getConfigurationParameters() {
        return [
            [AbstractConnector::OPTION_NAME => self::DB_HOST,
                AbstractConnector::OPTION_DESCRIPTION => "Host that runs the MySql server.",
                AbstractConnector::OPTION_MANDATORY => false,
                AbstractConnector::OPTION_DEFAULT => 'localhost'],
            [AbstractConnector::OPTION_NAME => self::DB_USER,
                AbstractConnector::OPTION_DESCRIPTION => "User used to authenticate on the server.",
                AbstractConnector::OPTION_MANDATORY => false,
                AbstractConnector::OPTION_DEFAULT => 'root'],
            [AbstractConnector::OPTION_NAME => self::DB_PASSWORD,
                AbstractConnector::OPTION_DESCRIPTION => "Password used for the authentication",
                AbstractConnector::OPTION_MANDATORY => false,
                AbstractConnector::OPTION_DEFAULT => ''],
            [AbstractConnector::OPTION_NAME => self::DB_PORT,
                AbstractConnector::OPTION_DESCRIPTION => "TCP port used by the MySql server.",
                AbstractConnector::OPTION_MANDATORY => false,
                AbstractConnector::OPTION_DEFAULT => 3306],
            [AbstractConnector::OPTION_NAME => self::DB_NAME,
                AbstractConnector::OPTION_DESCRIPTION => "Name of the database.",
                AbstractConnector::OPTION_MANDATORY => true,
                AbstractConnector::OPTION_DEFAULT => null],
        ];
    }

    /**
     * {@inheritdoc}
     * @see InterfaceConnector
     */
    static public function checkConfiguration(array $inConfiguration) {
        $set = new SpecificationsSet();

        foreach (self::getConfigurationParameters() as $_parameterSpec) {
            $name = $_parameterSpec[InterfaceConnector::OPTION_NAME];
            $mandatory = $_parameterSpec[InterfaceConnector::OPTION_MANDATORY];
            $set->addInputSpecification(new Specification($name, $mandatory, !$mandatory));
        }

        if ($set->check($inConfiguration)) {
            return [];
        }

        return array_values($set->getErrorsOnInputsInIsolationFromTheOthers());
    }

    /**
     * {@inheritdoc}
     * @see AbstractConnector
     */
    protected function _connect(array $inConfiguration) {

        // Check that all parameters are given.
        $parameters = [self::DB_HOST, self::DB_NAME, self::DB_PORT, self::DB_USER, self::DB_PASSWORD];
        $errors = [];
        foreach ($parameters as $_index => $_parameter) {
            if (! array_key_exists($_parameter, $inConfiguration)) {
                $errors[] = "Parameter ${_parameter} is missing";
            }
        }
        if (count($errors) > 0) {
            $exception = new \Exception(implode('. ', $errors));
            throw $exception;
        }

        // Then connect to the server.
        $host     = $inConfiguration[self::DB_HOST];
        $dbName   = $inConfiguration[self::DB_NAME];
        $port     = $inConfiguration[self::DB_PORT];
        $user     = $inConfiguration[self::DB_USER];
        $password = $inConfiguration[self::DB_PASSWORD];

        $pdo = null;

        try {
            $dsn = "mysql:host=${host};dbname=${dbName};port=${port}";
            $pdo = new \PDO($dsn, $user, $password);
        } catch (\PDOException $e) {
            $exception = new \Exception("Can not connect to the MySql database: " . $e->getMessage(), $e->getCode());
            throw $exception;
        }
        return $pdo;
    }
}