<?php

/**
 * This file implements the API's entry points' provider.
 */

namespace dbeurive\Backend\Database\EntryPoints;
use dbeurive\Backend\Database\DatabaseInterface;

/**
 * Class Provider
 *
 * This class implements the API's entry points provider.
 * It centralizes access to all the API's entry points.
 *
 * @package dbeurive\Backend\Database\EntryPoints
 */

class Provider
{
    /**
     * @var string Path to the repository that lists all known SQL requests.
     */
    private $__sqlRepositoryBasePath = null;
    /**
     * @var string Path to the repository that lists all known procedures.
     */
    private $__procedureRepositoryBasePath = null;
    /**
     * @var string Base namespace for all SQL requests.
     */
    private $__sqlBaseNameSpace = null;
    /**
     * @var string Base namespace for all database procedures.
     */
    private $__procedureBaseNameSpace = null;
    /**
     * @var string Name of the data interface.
     */
    private $__dataInterfaceName = null;

    /**
     * Create a provider.
     * @param string $inDataInterfaceName Name of the data interface.
     */
    public function __construct($inDataInterfaceName) {
        $this->__dataInterfaceName = $inDataInterfaceName;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the base namespace for the SQL requests.
     * @param string $inNameSpace The namespace to set.
     */
    public function setSqlBaseNameSpace($inNameSpace)
    {
        $inNameSpace = trim($inNameSpace, '\\');
        $this->__sqlBaseNameSpace = '\\' . $inNameSpace;
    }

    /**
     * Set the base namespace for the database procedures.
     * @param string $inNameSpace The namespace to set.
     */
    public function setProcedureBaseNameSpace($inNameSpace)
    {
        $inNameSpace = trim($inNameSpace, '\\');
        $this->__procedureBaseNameSpace = '\\' . $inNameSpace;
    }

    /**
     * Set the path to the directory used to store the SQL requests' definitions.
     * @param string $inPath Path to the directory used to store the SQL requests' definitions.
     */
    public function setSqlRepositoryBasePath($inPath)
    {
        $this->__sqlRepositoryBasePath = rtrim($inPath, DIRECTORY_SEPARATOR);;
    }

    /**
     * Set the path to the directory used to store all the procedures' definitions.
     * @param string $inPath Path to the directory used to store the procedures' definitions.
     */
    public function setProcedureRepositoryBasePath($inPath)
    {
        $this->__procedureRepositoryBasePath = rtrim($inPath, DIRECTORY_SEPARATOR);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the path to the repository that lists all known SQL requests.
     * Please note that this method is used __SOLELY__ by the CLI tool that executes unit tests for all API's entry point.
     * @return string
     */
    public function getSqlRepositoryBasePath()
    {
        return $this->__sqlRepositoryBasePath;
    }

    /**
     * Return the path to the repository that lists all known procedures.
     * Please note that this method is used __SOLELY__ by the CLI tool that executes unit tests for all API's entry point.
     * @return string
     */
    public function getProcedureRepositoryBasePath()
    {
        return $this->__procedureRepositoryBasePath;
    }

    /**
     * Return an SQL request identified by its name.
     * @param string $inName Name of the SQL request.
     * @return mixed The method returns an instance of the SQL request.
     * @throws \Exception
     */
    public function getSql($inName)
    {
        self::__checkConfiguration();
        // You may, or may not, registered the entry points for auto loading.
        // If you did not, then the following line is required.
        require_once $this->__sqlRepositoryBasePath . DIRECTORY_SEPARATOR . $inName . '.php';
        $class = self::__getFullyQualifiedClassName($this->__sqlBaseNameSpace, $inName);
        $class = new \ReflectionClass($class);
        $sql = $class->newInstanceArgs();
        return $sql;
    }

    /**
     * Return a procedure identified by its name.
     * @param string $inName Name of the procedure.
     * @return mixed The method returns an instance of the procedure.
     * @throws \Exception
     */
    public function getProcedure($inName)
    {
        self::__checkConfiguration();
        // You may, or may not, registered the entry points for auto loading.
        // If you did not, then the following line is required.
        require_once $this->__sqlRepositoryBasePath . DIRECTORY_SEPARATOR . $inName . '.php';
        $class = self::__getFullyQualifiedClassName($this->__procedureBaseNameSpace, $inName);
        $class = new \ReflectionClass($class);
        $procedure =  $class->newInstanceArgs();
        return $procedure;
    }

    /**
     * Returns the list of all documentations for SQL requests.
     * @return array The method returns an array that contains all documentations for SQL requests.
     *         Elements' type is: \dbeurive\Backend\Database\EntryPoints\Description\Sql
     * @throws \Exception
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql
     */
    public function getAllSqlDescriptions()
    {
        self::__checkConfiguration();
        return self::__getDescriptions($this->__sqlRepositoryBasePath, $this->__sqlBaseNameSpace);
    }

    /**
     * Returns the list of all documentations for the database procedure.
     * @return array The method returns an array that contains all documentations for database procedures.
     *         Elements' type is: \dbeurive\Backend\Database\EntryPoints\Description\Procedure
     * @throws \Exception
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure
     */
    public function getAllProceduresDescriptions()
    {
        self::__checkConfiguration();
        return self::__getDescriptions($this->__procedureRepositoryBasePath, $this->__procedureBaseNameSpace);
    }

    /**
     * Return the name of an API's entry point from a given path.
     * @param string $inAbsolutePath Absolute path to the API's entry point.
     * @return string The method returns the name of the API's entry point.
     * @throws \Exception
     */
    public function getNameFromPath($inAbsolutePath)
    {
        // Sanity checks

        if (0 == strlen($this->__procedureRepositoryBasePath)) {
            throw new \Exception("You did not set the base path to the procedures' base directory! You need to set this value if you want to use the method getName() of a procedure's class. This is done through the class \"dbeurive\\Backend\\database\\ServiceProvider\".");
        }

        if (0 == strlen($this->__sqlRepositoryBasePath)) {
            throw new \Exception("You did not set the base path to the requests' base directory!  You need to set this value if you want to use the method getName() of a request's class. This is done through the class \"dbeurive\\Backend\\database\\ServiceProvider\".");
        }

        $isProcedure = 0 === strpos($inAbsolutePath, $this->__procedureRepositoryBasePath);
        $isSql = 0 === strpos($inAbsolutePath, $this->__sqlRepositoryBasePath);

        if ((!$isProcedure) && (!$isSql)) {
            throw new \Exception("Invalid path \"$inAbsolutePath\": this is not an API's entry point!\nBase pathes are :\n* \"" .
                $this->__procedureRepositoryBasePath . "\",\n* \"" . $this->__sqlRepositoryBasePath . '".');
        }

        $basePath = $isProcedure ? $this->__procedureRepositoryBasePath : $this->__sqlRepositoryBasePath;
        return ltrim(substr(substr($inAbsolutePath, strlen($basePath)), 0, -4), '/');
    }

    /**
     * Return a string that represents the configuration.
     * @return string The method returns a string that represents the configuration.
     */
    public function getDebugConfig()
    {
        $data = [
            'Configuration for the entry point provider "' . $this->__dataInterfaceName . '"',
            'Repository path for SQL => ' . $this->__sqlRepositoryBasePath,
            'Repository path for procedures => ' . $this->__procedureRepositoryBasePath,
            'Base namespace for SQL => ' . $this->__sqlBaseNameSpace,
            'Base namespace for procedures => ' . $this->__procedureBaseNameSpace
        ];
        return implode("\n", $data);
    }

    /**
     * Return the the database interface linked to this provider.
     * This method is used within the entry points, in order to access the database's structure.
     * @return DatabaseInterface The method returns the database interface linked to this provider.
     */
    public function getDataInterface() {
        return \dbeurive\Backend\Database\DatabaseInterface::getInstance($this->__dataInterfaceName);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Private methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This method checks the service's configuration.
     * @throws \Exception
     */
    private function __checkConfiguration()
    {
        $errors = array();
        $toCheck = array(
            'path to the repository that lists all known SQL requests' => array(
                'v' => $this->__sqlRepositoryBasePath,
                'f' => __CLASS__ . '\setSqlRepositoryBasePath'),
            'path to the repository that lists all known procedures' => array(
                'v' => $this->__procedureRepositoryBasePath,
                'f' => __CLASS__ . '\setProcedureRepositoryBasePath'),
            'base namespace for all SQL requests' => array(
                'v' => $this->__sqlBaseNameSpace,
                'f' => __CLASS__ . '\setSqlBaseNameSpace'),
            'base namespace for all database procedures' => array(
                'v' => $this->__procedureBaseNameSpace,
                'f' => __CLASS__ . '\setProcedureBaseNameSpace')
        );
        foreach ($toCheck as $_message => $_value) {
            if (is_null($_value['v'])) {
                $errors[] = "The ${_message} is not set. Please call the static method {$_value['f']}.";
            }
        }
        if (count($errors) > 0) {
            $message = implode("\n", $errors);
            throw new \Exception($message);
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Static methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Get the list of all descriptions within a given directory.
     * @param string $inDirectoryPath Absolute path to the directory that contains descriptions.
     * @param string $inBaseNameSpace Base namespace for the elements.
     * @return array The method returns an array of descriptions.
     *         The returned array is a list of \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription.
     * @see \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription
     */
    private function __getDescriptions($inDirectoryPath, $inBaseNameSpace)
    {
        $directory = new \RecursiveDirectoryIterator($inDirectoryPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $php = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        $prefixLenght = strlen($inDirectoryPath) + 1;

        $descriptions = array();
        foreach ($php as $_relativeFilePath => $_fileInfo) {

            $name = substr($_relativeFilePath, $prefixLenght, -4);
            // You may, or may not, registered the entry points for auto loading.
            // If you did not, then the following line is required.
            require_once $_relativeFilePath;
            $class = self::__getFullyQualifiedClassName($inBaseNameSpace, $name);
            $class = new \ReflectionClass($class);
            /* @var AbstractSql $element */
            $element = $class->newInstanceArgs();
            $element->setFieldsProvider( function($inName) { return $this->getDataInterface()->getTableFieldsNames($inName); } );
            /** @var \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription $description */
            $description = $element->getDescription();
            $description->setName_($name);
            $descriptions[] = $description;
        }

        return $descriptions;
    }

    /**
     * Create a fully qualified class name given a base namespace and a "relative" (to a base directory) class name.
     * @param string $inBaseNameSpace Base namespace.
     * @param string $inElementName "Relative" class name.
     * @return string The method returns a fully qualified class name.
     */
    static private function __getFullyQualifiedClassName($inBaseNameSpace, $inElementName)
    {
        $className = $inBaseNameSpace . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $inElementName);

        if (!class_exists($className)) {
            throw new \Exception("The class \"${className}\" does not exist ! Please check for possible errors:\n  (1) Check the namespaces declared within your configuration\n  (2) Check the namespaces declared in all API's classes.");
        }
        return $className;
    }
}