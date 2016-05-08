<?php

/**
 * This file defines the static interface for all "documentation writer".
 * 
 * Please note that it is not possible to define static methods from within an abstract class.
 * The static interface we need to impose is defined by this interface, which is declared as « implemented » by the abstract class.
 */

namespace dbeurive\Backend\Cli\Adapter\Database\DocWriter;

/**
 * Interface InterfaceDocWriter
 *
 * This interface defines the static interface for all "documentation writer".
 *
 * @package dbeurive\Backend\Cli\Adapter\Database\DocWriter
 */

interface InterfaceDocWriter {

    /**
     * This method checks a given configuration.
     * @param array $inConfiguration The given configuration.
     * @return array The method returns an array that represents a list of error messages.
     *               `[<first error message>, <second error message>...]`.
     *               If the given configuration is valid, then the method returns an empty array (`[]`).
     */
    static public function checkConfiguration(array $inConfiguration);
}