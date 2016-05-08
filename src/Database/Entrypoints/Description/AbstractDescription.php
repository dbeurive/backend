<?php

/**
 * This file implements the base class for the descriptions of all API's entry points (SQL requests or procedures).
 * Please note that a "description" (for an API's entry point) contains "elements" (the tables, the fields, the tags, the entities and the actions).
 */

namespace dbeurive\Backend\Database\Entrypoints\Description;

/**
 * Class AbstractDescription
 *
 * This class is the base class for the descriptions of all API's entry points (SQL requests or procedures).
 * Please note that a "description" (for an API's entry point) contains "elements" (the tables, the fields, the tags, the entities and the actions).
 *
 * @package dbeurive\Backend\Database\Entrypoints\Description
 */

abstract class AbstractDescription {

    const KEY_NAME        = 'name';
    const KEY_DESCRIPTION = 'description';

    /**
     * @var string Name the API' entry point.
     *      Please note that the names of the API's entry points are calculated (from the fully qualified name of the class the implements the API's entry point).
     */
    private $__name = null;
    /**
     * @var string Description of the API' entry point.
     */
    private $__description = null;
    /**
     * @var integer Table's ID for the API's entry point's description.
     */
    private $__id = null;
    /**
     * @var array List of tags that applies to the API's entry point being described (this is an array of strings).
     */
    private $__tags = [];
    /**
     * @var array List of relations "entity -> action" (as defined by the concept of "Entity-Relationship Modeling") for the API's entry point being described.
     *      This variable is an associative array:
     *      * Keys are the names of the entities.
     *      * Values are the list of actions.
     */
    private $__entitiesActions = [];
    /**
     * @var array List of returned data values.
     * Please note that a "data value" is a value that is calculated and returned by the SGBDR.
     * A "data value" is not a field's value.
     * SQL requests and procedures may return "data values".
     */
    private $__outputDataValues = [];
    /**
     * @var array List of all created descriptions.
     */
    static private $__repository = [];
    /**
     * @var array This array contains all the tables' fields declared in the database.
     *      List of \dbeurive\Backend\Database\Entrypoints\Description\Element\Field
     *      Please note that this list of fields should come from the analyses of the database' schema.
     *      Thus, it is the exhaustive list of all fields within the database.
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Element\Field
     */
    static protected $_allDbFields = null;
    /**
     * @var array This array contains all the database's fields, sorted by tables' names.
     */
    static protected $_fieldsByTable = [];
    /**
     * @var array List of all identified entities (as defined by the concept of "Entity-Relationship Modeling").
     */
    private static $__entities = [];
    /**
     * @var array List of all identified actions (as defined by the concept of "Entity-Relationship Modeling").
     */
    private static $__actions = [];

    // -----------------------------------------------------------------------------------------------------------------
    // User's setters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This method sets the textual description of the API's entry point being described.
     * @param string $inDescription Textual description to set.
     * @return $this
     */
    public function setDescription($inDescription) {
        $this->__description = $inDescription;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // User's Adders.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This methods adds tags to the API's entry point being described.
     * @return $this
     */
    public function addTags() {
        $this->__tags = array_merge($this->__tags, func_get_args());
        return $this;
    }

    /**
     * Add a "data value" to the description of the "data sets" returned by the entry point being described.
     * Please note that a "data value" is a value that is calculated and returned by the SGBDR.
     * A "data value" is not a field's value.
     *
     * @param string $inValueName Name of the value.
     * @param string|null $inValueDescription Description of the value.
     * @return $this
     */
    public function addOutputDataValue($inValueName, $inValueDescription=null) {
        $this->__outputDataValues[] = [ self::KEY_NAME => $inValueName,
            self::KEY_DESCRIPTION => $inValueDescription];
        return $this;
    }

    /**
     * Add a relation "entity -> action" (as defined by the concept of "Entity-Relationship Modeling") to API's entry point being described.
     * @param string $inEntityName Name of the entity.
     * @param string $inActionName of the action.
     * @param string $inAction,... unlimited OPTIONAL number of additional names of actions.
     * return $this
     */
    public function addEntityActionsRelationship($inEntityName, $inAction) {

        $actions = array_slice(func_get_args(), func_num_args()-1);

        // Add entities and services into the lists of all identified entities and services.
        if (! in_array($inEntityName, self::$__entities)) {
            self::$__entities[] = $inEntityName;
        }
        self::$__actions = array_unique(array_merge(self::$__actions, $actions));

        if (! array_key_exists($inEntityName, $this->__entitiesActions)) {
            $this->__entitiesActions[$inEntityName] = $actions;
            return $this;
        }

        $this->__entitiesActions[$inEntityName] = array_unique(array_merge($this->__entitiesActions[$inEntityName], $actions));
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEV's getters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the list of tags defined for the API's entry point.
     * @return array The method returns the list of tags.
     */
    public function getTags_() {
        return $this->__tags;
    }

    /**
     * This method returns the textual description for the API's entry point.
     * @return string The textual description of the element.
     */
    public function getDescription_() {
        return $this->__description;
    }

    /**
     * This method returns the name of the API's entry point.
     * Please note that the names of the API's entry points are calculated (from the fully qualified name of the class the implements the API's entry point).
     * @return string The name of the element.
     */
    public function getName_() {
        return $this->__name;
    }

    /**
     * Return the database's (unique) ID assigned to the description.
     * @return integer The element's ID.
     */
    public function getId_() {
        return $this->__id;
    }

    /**
     * Return the list of output "data values" for the API's entry point.
     * Please note that a "data value" is a value that is calculated and returned by the SGBDR.
     * A "data value" is not a field's value.
     *
     * @return array The method returns the list of output "data values" for this entry point.
     *         Please not that each element of the returned list is an associative array that presents the following keys:
     *         \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::KEY_NAME: the name of the value.
     *         \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::KEY_DESCRIPTION: the description for the value.
     * @see \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::KEY_NAME
     * @see \dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription::KEY_DESCRIPTION
     */
    public function getOutputDataValues_() {
        return $this->__outputDataValues;
    }

    /**
     * Return the list of relations "entity -> action(s)" (as defined by the concept of "Entity-Relationship Modeling") for the API's entry point.
     * @return array The returned value is an associative array.
     *         * Keys are the names of the entities.
     *         * Values are the lists of actions (strings).
     */
    public function getEntitiesActions_() {
        return $this->__entitiesActions;
    }

    /**
     * Return the list of all identified "entities" (as defined by the concept of "Entity-Relationship Modeling"), for all described API's entry points.
     * @return array
     */
    public static function getAllIdentifiedEntities_() {
        return self::$__entities;
    }

    /**
     * Return the list of all identified actions (as defined by the concept of "Entity-Relationship Modeling"), for all described API's entry points.
     * @return array
     */
    public static function getAllIdentifiedActions_() {
        return self::$__actions;
    }

    /**
     * Add a description to the descriptions' repository.
     * @throws \Exception
     */
    public function addToRepository_() {
        if (is_null($this->__name)) {
            throw new \Exception("Can not add an unnamed description to the requests' repository.");
        }
        $class = get_class($this);
        if (! array_key_exists($class, self::$__repository)) {
            self::$__repository[$class] = array();
        }
        self::$__repository[$class][$this->__name] = $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEV' setters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This method set the list of all listed database's fields for an API's entry point.
     * @param array $inFields List of all listed database's fields.
     *        This is a list of \dbeurive\Backend\Database\Entrypoints\Description\Element\Field
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Element\Field
     */
    static public function setDbFields_(array $inFields) {
        self::$_allDbFields = $inFields;
        /* @var \dbeurive\Backend\Database\Entrypoints\Description\Element\Field $_field */
        foreach ($inFields as $_field) {
            $tableName = $_field->getTable()->getName();
            if (! array_key_exists($tableName, self::$_fieldsByTable)) {
                self::$_fieldsByTable[$tableName] = array();
            }
            self::$_fieldsByTable[$tableName][$_field->getName()] = $_field;
        }
    }

    /**
     * Set the description's ID (within the database).
     * @param integer $inId ID of the description.
     * @return $this
     */
    public function setId_($inId) {
        $this->__id = $inId;
        return $this;
    }

    /**
     * This method sets the name of the API's entry point.
     * Please note that the names of the API's entry points are calculated (from the fully qualified name of the class the implements the API's entry point).
     * @param string $inName Name of the API's entry point.
     * @return $this
     */
    public function setName_($inName) {
        $this->__name = $inName;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEV's testers.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This method checks the description.
     * @param string $outError Reference to a string used to store an error message.
     * @return bool If the description is  valid, then the method returns the value true.
     *         Otherwise it returns the value false. In this case, the string `$outError` should contain an error message.
     * @throws \Exception
     */
    public function check(&$outError) {
        $lists = $this->_getAllDdFieldsListsReferences();
        foreach ($lists as &$_lists) {
            if (false === $this->_checkFields($_lists, $outError)) {
                return false;
            }
        }
        return true;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEV' static methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the fully qualified name of the class that represents the description for the API's entry point.
     * Please not that this method should return the value of __CLASS__.
     * @return string The fully qualified name of the description's class. It could be:
     *           * "\dbeurive\Backend\Database\Entrypoints\Description\Procedure"
     *           * "\dbeurive\Backend\Database\Entrypoints\Description\Sql"
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Procedure
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Sql
     */
    public static function getFullyQualifiedClassName_() {
        $reflector = new \ReflectionClass(get_called_class());
        return $reflector->getName();
    }

    /**
     * Search for a description in the descriptions' repository.
     * @param string $inClass Description's class. It could be:
     *           * "\dbeurive\Backend\Database\Entrypoints\Description\Procedure"
     *           * "\dbeurive\Backend\Database\Entrypoints\Description\Sql"
     * @param string $inName Description's name.
     * @return bool|\dbeurive\Backend\Database\Entrypoints\Description\AbstractDescription If the description is found, then the method returns it.
     *         Otherwise the method returns the value false.
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Procedure
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Sql
     */
    static public function getByClassAndName_($inClass, $inName) {
        $inClass = ltrim($inClass, '\\');
        if (! array_key_exists($inClass, self::$__repository)) {
            return false;
        }
        if (! array_key_exists($inName, self::$__repository[$inClass])) {
            return false;
        }
        return self::$__repository[$inClass][$inName];
    }

    /**
     * Reset the description's manager.
     */
    static public function reset() {
        self::$__repository = [];
        self::$_allDbFields = null;
        self::$_fieldsByTable = [];
        self::$__entities = [];
        self::$__actions = [];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Abstract methods.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * This method returns all the tables' fields associated with the description.
     * @return array List of all the tables' fields associated with the description.
     *         The returned array is an array of strings "<table name>.<field name>".
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Procedure
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Sql
     */
    abstract protected function _getDbFields();

    /**
     * This method returns an array of references.
     * Each reference in the returned array references to an array.
     * Each referenced array contains a list of database's fields used by the SQL request.
     * Please, make sure to return an array of __REFERENCES__.
     * @return array The method returns an array of references to arrays.
     *         Example: $updated = array();
     *                  $selected = array();
     *                  $condition = array();
     *                  ...
     *                  return array(&$updated, &$selected, &$condition);
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Procedure
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Sql
     */
    abstract protected function _getAllDdFieldsListsReferences();

    /**
     * Check and expand the fields' names.
     * @param array $inFields List of fields' names to process.
     *        This array is an array of strings "<table name>.<field name>".
     * @param string $outError Reference to a string used to store an error message.
     *        Please note that the structure of the list of fields depends on the API's description being checked.
     * @return bool If the fields are valid, then the method returns the value true.
     *         Otherwise it returns the value false. In this case, the string `$outError` should contain an error message.
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Procedure
     * @see \dbeurive\Backend\Database\Entrypoints\Description\Sql
     */
    abstract protected function _checkFields(array &$inFields, &$outError);
}