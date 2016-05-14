<?php

namespace dbeurive\Backend\Database\Connector;


interface InterfaceConnector
{
    const OPTION_NAME = 'name';
    const OPTION_DESCRIPTION = 'description';
    const OPTION_MANDATORY = 'mandatory';
    const OPTION_DEFAULT = 'localhost';

    /**
     * This method returns the list of configuration's parameters required for this "database connector".
     * @return array|false If the method returns the value false, then it means that there is no need to return the list of options.
     *         Otherwise, the method returns an array.
     *         Each element of the returned array is an associative array that presents to entries.
     *         * \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_NAME: the name of the parameter.
     *         * \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_DESCRIPTION: the description of the parameter.
     *         * \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_MANDATORY: this entry specifies whether the parameter is mandatory or not.
     *         * \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_DEFAULT: this entry specifies the default value for the parameter.
     *
     * @see \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_NAME
     * @see \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_DESCRIPTION
     * @see \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_MANDATORY
     * @see \dbeurive\Backend\Database\Connector\InterfaceConnector::OPTION_DEFAULT
     */
    static public function getConfigurationParameters();

    /**
     * Check the configuration for the specific "database connector" being executed.
     * @param array $inConfiguration List of parameters that define the configuration to check.
     * @return array If the given configuration is valid, then the method returns an empty array.
     *         Otherwise, the method returns a list of error messages.
     */
    static public function checkConfiguration(array $inConfiguration);
}