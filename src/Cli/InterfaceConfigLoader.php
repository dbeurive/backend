<?php

/**
 * This file describes the interface for a "configuration loader".
 * A "configuration loader" is just a software component that returns an array that represents a configuration.
 */

namespace dbeurive\Backend\Cli;

/**
 * Interface InterfaceConfigLoader
 *
 * This interface defines a "configuration loader".
 * A "configuration loader" is just a software component that returns an array that represents a configuration.
 *
 * @package dbeurive\Backend\Cli
 */

interface InterfaceConfigLoader
{
    /**
     * Load and return the configuration.
     * @return array The method returns an array that represents a configuration.
     */
    public function load();
}