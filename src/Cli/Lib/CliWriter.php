<?php

/**
 * This file implements a very basic API used to print messages to the console.
 */

namespace dbeurive\Backend\Cli\Lib;

/**
 * Class CliWriter
 *
 * This class contains methods used to write messages to the console.
 *
 * @package dbeurive\Backend\Cli\Lib
 */

class CliWriter {

    /**
     * @var callable Specific function used to signal an informative message.
     *      The function's signature must be: `void function(string $message)`.
     */
    static private $__infoWriter = null;
    /**
     * @var callable Specific function used to signal an error message.
     *      The function's signature must be: `void function(string $message)`.
     */
    static private $__errorWriter = null;
    /**
     * @var callable Specific function used to signal that an action was successful.
     *      The function's signature must be: `void function(string $message)`.
     */
    static private $__successWriter = null;

    /**
     * Initialize the output writer.
     * @param callable $inInfoWriter Function used to writer informative messages.
     * @param callable $inErrorWriter Function used to writer error messages.
     * @param callable $inSuccessWriter Function used to writer success messages.
     */
    static public function init($inInfoWriter=null, $inErrorWriter=null, $inSuccessWriter=null) {
        $default = function($inMessage) { echo $inMessage; };
        self::$__infoWriter    = is_null($inInfoWriter)    ? $default : $inInfoWriter;
        self::$__errorWriter   = is_null($inErrorWriter)   ? $default : $inErrorWriter;
        self::$__successWriter = is_null($inSuccessWriter) ? $default : $inSuccessWriter;
    }

    /**
     * Print an information.
     * @param string $inMessage Message to print.
     * @param bool $inOptNewLine This tag indicates whether the message should end with a new line.
     * @return string
     */
    static public function echoInfo($inMessage, $inOptNewLine=true) {
        $last = $inOptNewLine ? "\n" : '';
        echo $inMessage . $last;
    }

    /**
     * Print an error.
     * @param string $inMessage Message to print.
     * @param bool $inOptNewLine This tag indicates whether the message should end with a new line.
     * @return string
     */
    static public function echoError($inMessage, $inOptNewLine=true) {
        $last = $inOptNewLine ? "\n" : '';
        echo $inMessage . $last;
    }

    /**
     * Print a success' message.
     * @param string $inMessage Message to print.
     * @param bool $inOptNewLine This tag indicates whether the message should end with a new line.
     * @return string
     */
    static public function echoSuccess($inMessage, $inOptNewLine=true) {
        $last = $inOptNewLine ? "\n" : '';
        echo $inMessage . $last;
    }
}
