<?php

/**
 * This file implements the base class for all "pieces of information" (called "elements") stored within the generated SQLite database.
 */

namespace dbeurive\Backend\Database\Entrypoints\Description\Element;

/**
 * Class AbstractElement
 *
 * This class is the base class for all "pieces of information" (called "elements") stored within the generated SQLite database.
 * All "elements" have a unique ID and a name.
 *
 * @package dbeurive\Backend\Database\Entrypoints\Description\Element
 */

abstract class AbstractElement {
    /**
     * @var array Elements' repository.
     * This property is an associative array which structure is:
     * [
     *      <fully qualified class name (of elements)> => [
     *             <element's name> => <element>,
     *             <element's name> => <element>
     *             ...
     *      ],
     *      <fully qualified class name (of elements)> => [
     *             <element's name> => <element>,
     *             <element's name> => <element>
     *             ...
     *      ],
     *      ...
     * ]
     */
    static private $__repository = array();
    /**
     * @var string Name of the element.
     */
    private $__name = null;
    /**
     * @var integer ID of the element.
     */
    private $__id = null;

    /**
     * Build the element.
     * @param string $inName Name of the element.
     * @param integer $inId IF of the element.
     */
    abstract public function __construct($inName, $inId=null);

    /**
     * Set the name of this element.
     * @param string $inName Element's name.
     */
    public function setName($inName) {
        $this->__name = $inName;
    }

    /**
     * Return the name of this element.
     * @return string The method returns the name of the element.
     */
    public function getName() {
        return $this->__name;
    }

    /**
     * Set the ID of this element.
     * @param integer $inId Element's ID.
     * @return $this
     */
    public function setId($inId) {
        $this->__id = $inId;
        return $this;
    }

    /**
     * Return the ID of this element.
     * @return integer The method returns the element's ID.
     */
    public function getId() {
        return $this->__id;
    }

    /**
     * Add this element to the elements' repository.
     * @param string $inName Name of the element to add to the repository.
     * @throws \Exception
     * @see $__repository
     */
    public function addToRepository($inName=null) {
        $inName = is_null($inName) ? $this->__name : $inName;
        if (is_null($inName)) {
            throw new \Exception("Can not add an unnamed description to the requests' repository.");
        }
        $class = get_class($this);
        if (! array_key_exists($class, self::$__repository)) {
            self::$__repository[$class] = array();
        }
        self::$__repository[$class][$inName] = $this;
    }

    /**
     * Search for an element in the elements' repository.
     * @param string $inClass Element's class.
     * @param string $inName Element's name.
     * @return bool|AbstractElement If the element is found, then the method returns it.
     *         Otherwise the method returns the value false.
     */
    static public function getByClassAndName($inClass, $inName) {
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
     * Return the repository's entries for a given class' name.
     * @param string $inClass Class' name.
     * @return array|false The method returns the list of entries in the repository for the given class' name.
     *         If the given class' name is not found, then the method returns the value false.
     */
    static public function getRepositoryForClass($inClass) {
        $inClass = ltrim($inClass, '\\');
        if (! array_key_exists($inClass, self::$__repository)) {
            return false;
        }
        return self::$__repository[$inClass];
    }

    /**
     * Return the fully qualified name of the object's class.
     * Please note that this method should return the value of __CLASS_.
     * @return string The fully qualified name of the element's class.
     */
    public static function getFullyQualifiedClassName() {
        $reflector = new \ReflectionClass(get_called_class());
        return $reflector->getName();
    }
}