<?php

/**
 * This file defines the configuration parameters that can be used by all CLI tools.
 */

namespace dbeurive\Backend\Cli;

/**
 * Class Option
 *
 * This class contains the options that are specific to the CLI tools.
 *
 * @package dbeurive\Backend\Cli
 */

class Option {

    /**
     * This option represents the name of the class used to load a configuration.
     * It refers to a "configuration loader".
     */
    const CONFIG_LOADER_CLASS_NAME = 'config-loader';
}