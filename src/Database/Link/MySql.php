<?php

/**
 * This file implements the "database link" to a MaySql database.
 */

namespace dbeurive\Backend\Database\Link;

/**
 * Class MySql
 *
 * This class implements the "database link" to a MaySql database.
 *
 * @package dbeurive\Backend\Database\Link
 */

class MySql extends AbstractLink
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
     * @see AbstractLink
     */
    public function __construct() {

    }

    /**
     * {@inheritdoc}
     * @see AbstractLink
     */
    public function getConfigurationOptions() {
        return [
            [self::OPTION_NAME => self::DB_HOST,     self::OPTION_DESCRIPTION => "Host that runs the MySql server."],
            [self::OPTION_NAME => self::DB_USER,     self::OPTION_DESCRIPTION => "User used to authenticate on the server."],
            [self::OPTION_NAME => self::DB_PASSWORD, self::OPTION_DESCRIPTION => "Password used for the authentication"],
            [self::OPTION_NAME => self::DB_PORT,     self::OPTION_DESCRIPTION => "TCP port used by the MySql server."],
            [self::OPTION_NAME => self::DB_NAME,     self::OPTION_DESCRIPTION => "Name of the database."]
        ];
    }

    /**
     * {@inheritdoc}
     * @see AbstractLink
     */
    public function quoteValue($inValue) {
        /** @var \PDO $pdo */
        $pdo = $this->_getDatabaseConnexionHandler();
        if (is_null($pdo)) {
            throw new \Exception("You did not initialize the database handler!");
        }
        return $pdo->quote($inValue);
    }

    /**
     * {@inheritdoc}
     * @see AbstractLink
     */
    public function quoteFieldName($inFieldName) {
        $tokens = explode('.', $inFieldName);
        if (2 != count($tokens)) {
            throw new \Exception("Invalid field's name ${inFieldName}");
        }
        return '`' . $tokens[0] . '`' . '.' . '`' . $tokens[1] . '`';
    }

    /**
     * {@inheritdoc}
     * @see AbstractLink
     */
    public function getDatabaseSchema() {
        $error = null;
        $schema = $this->__getTables($this->_getConfiguration(), $error);
        if (false === $schema) {
            $this->_setErrorMessage($error);
            return false;
        }
        return $schema;
    }

    /**
     * {@inheritdoc}
     * @see AbstractLink
     */
    protected function _checkConfiguration(array $inConfiguration) {
        $errors = [];
        if (! array_key_exists(self::DB_HOST, $inConfiguration)) {
            $errors[] = "Option " . self::DB_HOST . " is missing.";
        }

        if (! array_key_exists(self::DB_NAME, $inConfiguration)) {
            $errors[] = "Option " . self::DB_NAME . " is missing.";
        }

        if (! array_key_exists(self::DB_PORT, $inConfiguration)) {
            $errors[] = "Option " . self::DB_PORT . " is missing.";
        }

        if (! array_key_exists(self::DB_USER, $inConfiguration)) {
            $errors[] = "Option " . self::DB_USER . " is missing.";
        }

        if (! array_key_exists(self::DB_PASSWORD, $inConfiguration)) {
            $errors[] = "Option " . self::DB_PASSWORD . " is missing.";
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     * @see AbstractLink
     */
    protected function _connect(array $inConfiguration) {

        if (! is_null($this->_databaseConnexionHandler)) {
            return true;
        }

        $host     = $inConfiguration[self::DB_HOST];
        $dbName   = $inConfiguration[self::DB_NAME];
        $port     = $inConfiguration[self::DB_PORT];
        $user     = $inConfiguration[self::DB_USER];
        $password = $inConfiguration[self::DB_PASSWORD];

        try {
            $dsn = "mysql:host=${host};dbname=${dbName};port=${port}";
            $this->_databaseConnexionHandler = new \PDO($dsn, $user, $password);
        } catch (\PDOException $e) {
            $this->_setErrorMessage("Can not connect to the database: " . $e->getMessage());
            $this->_setErrorCode($e->getCode());
            return false;
        }
        return true;
    }

    /**
     * Returns the list of fields in the MySql database.
     * @param array $inConfig Configuration for "database link".
     * @param string $outError String used to store an error message.
     * @return array Upon successful completion the method returns an array.
     *               array(<table name> => array(<field name>, <field name>...),
     *                     <table name> => array(<field name>, <field name>...),
     *                     ...)
     *               Otherwise, it returns the value false.
     */
    private function __getTables(array $inConfig, &$outError) {

        $result   = array();
        $pdo      = null;
        $outError = null;

        /** @var \PDO $pdo */
        $pdo = $this->_getDatabaseConnexionHandler();
        if (is_null($pdo)) {
            $outError = "You did not open a connexion to the database.";
            return false;
        }

        $sql = "select TABLE_NAME from information_schema.tables where TABLE_SCHEMA={$pdo->quote($inConfig[self::DB_NAME])}";
        $tables = $pdo->query($sql);
        foreach ($tables as $table) {
            $tableName = $table['TABLE_NAME'];
            $data[$tableName] = array();
            $sql = "desc `$tableName`";
            $_fields = $pdo->query($sql);
            foreach ($_fields as $_field) {
                $result[$tableName][] = $_field['Field'];
            }
        }

        return $result;
    }


}