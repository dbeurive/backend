<?php

namespace dbeurive\Backend\Database\Connector;


interface InterfaceConnector
{
    /**
     * Quote a field's name.
     * @param string $inFieldName Name of the field to quote.
     * @return string The method returns the quoted field.
     */
    static public function quoteFieldName($inFieldName);
}