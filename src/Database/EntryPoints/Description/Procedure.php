<?php

/**
 * This file implements the class that represents the description for a procedure.
 */


namespace dbeurive\Backend\Database\EntryPoints\Description;

/**
 * Class Procedure
 *
 * This class contains all the information that describes a procedure.
 * Please note that a procedure's configuration should not be complex.
 * However, in practice it could be (most likely due to a bad design) : some parameters or fields may be mandatory depending on a context of execution.
 * Therefore, the API does not require the user to declare fields or parameters to be always mandatory.
 *
 * @package dbeurive\Backend\Database\EntryPoints\Description
 */

class Procedure extends \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription
{
    const KEY_ALWAYS = 'always';

    /**
     * @var array List of SQL requests used by this procedure.
     */
    private $__requests = [];
    /**
     * @var array List of mandatory input fields.
     *      This is a list of arrays: "array(self::KEY_NAME => '<table name>.<field name>', self::KEY_DESCRIPTION => '...', self::KEY_ALWAYS => true|false)".
     *      Please note that a procedure's configuration should not be complex.
     *      However, in practice it could be (most likely due to a bad design) : some fields may be mandatory depending on a context of execution.
     *      Therefore, the API does not require the user to declare fields to be always mandatory.
     */
    private $__inMandatoryFields = [];
    /**
     * @var array List of optional input fields.
     *      This is a list of arrays: "array(self::KEY_NAME => '<table name>.<field name>', self::KEY_DESCRIPTION => '...')".
     *      Please note that a procedure's configuration should not be complex.
     *      Optional fields should always be optional.
     *      However, probably due to bad design, this may not be the case.
     *      You should declare as optional fields that are always optional.
     *      If a field may be mandatory (depending on the context of execution) you should declare it "occasionally mandatory".
     *      You do that by calling the method addMandatoryInputField(..., ..., false).
     * @see addMandatoryInputField
     */
    private $__inOptionalFields = [];
    /**
     * @var array List of mandatory input parameters.
     *      This is a list of arrays: "array(self::KEY_NAME => '<param name>', self::KEY_DESCRIPTION => '...', self::KEY_ALWAYS => true|false)".
     *      Please note that a procedure's configuration should not be complex.
     *      However, in practice it could be (most likely due to a bad design) : some parameters may be mandatory depending on a context of execution.
     *      Therefore, the API does not require the user to declare parameters to be always mandatory.
     */
    private $__inMandatoryParams = [];
    /**
     * @var array List of optional input parameters.
     *      This is a list of arrays: "array(self::KEY_NAME => '<param name>', self::KEY_DESCRIPTION => '...')".
     *      Please note that a procedure's configuration should not be complex.
     *      Optional parameters should always be optional.
     *      However, probably due to bad design, this may not be the case.
     *      You should declare as optional parameters that are always optional.
     *      If a parameter may be mandatory (depending on the context of execution) you should declare it "occasionally mandatory".
     *      You do that by calling the method addMandatoryInputParam(..., ..., false).
     * @see addMandatoryInputParam
     */
    private $__inOptionalParams = [];
    /**
     * @var array List of output fields.
     *      This is a list of arrays: "array(self::KEY_NAME => '<table name>.<field name>', self::KEY_DESCRIPTION => '...')".
     */
    private $__outputFields = [];
    /**
     * @var array List of "output values".
     *      Please note that an "output value" is a value that is calculated by the procedure.
     *      An "output value" is not returned by the SGBDR. Values returned by the SGBDR are called "data values".
     */
    private $__outputValues = [];
    /**
     * @var bool This flag indicates whether the result of the procedure is an array of rows.
     */
    private $__outIsMultiRow = [];

    // -----------------------------------------------------------------------------------------------------------------
    // User's adders.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Add a request to the list of requests used by this procedure.
     * @param string $inRequest Name of the request to add.
     * @return $this
     */
    public function addRequest($inRequest) {
        $this->__requests[] = $inRequest;
        return $this;
    }

    /**
     * Add a mandatory input field for this procedure.
     * Please see the comment for the property $__inMandatoryFields.
     *
     * @param string $inFieldName Name of the field.
     * @param string|null $inDescription Description of the field.
     * @param bool $inOprMandatory Specify whether the field is always mandatory or not.
     * @return $this
     * @see $__inMandatoryFields
     */
    public function addMandatoryInputField($inFieldName, $inDescription=null, $inOptAlwaysMandatory=true) {
        $this->__inMandatoryFields[] = array(self::KEY_NAME => $inFieldName,
            self::KEY_DESCRIPTION => $inDescription,
            self::KEY_ALWAYS => $inOptAlwaysMandatory);
        return $this;
    }

    /**
     * Add an optional input field for this procedure.
     * Please see the comment for the property $__inOptionalFields.
     *
     * @param string $inFieldName Name of the field.
     * @param string|null $inDescription Description of the field.
     * @return $this
     * @see $__inOptionalFields
     */
    public function addOptionalInputField($inFieldName, $inDescription=null) {
        $this->__inOptionalFields[] = array(self::KEY_NAME => $inFieldName,
            self::KEY_DESCRIPTION => $inDescription);
        return $this;
    }

    /**
     * Add a mandatory input parameter to this procedure.
     * Please see the comment for the property $__inMandatoryParams.
     *
     * @param string $inParamName Parameter's name.
     * @param string|null $inDescription Parameter's description.
     * @param bool $inOprMandatory Specify whether the parameter is always mandatory or not.
     * @return $this
     * @see $__inMandatoryParams
     */
    public function addMandatoryInputParam($inParamName, $inDescription=null, $inOptAlwaysMandatory=true) {
        $this->__inMandatoryParams[] = array(self::KEY_NAME => $inParamName,
            self::KEY_DESCRIPTION => $inDescription,
            self::KEY_ALWAYS => $inOptAlwaysMandatory);
        return $this;
    }

    /**
     * Add an optional input parameter to the procedure.
     * Please see the comment for the property $__inOptionalParams.
     *
     * @param string $inParamName Parameter's name
     * @param string|null $inDescription Parameter's description.
     * @return $this
     * @see $__inOptionalParams
     */
    public function addOptionalInputParam($inParamName, $inDescription=null) {
        $this->__inOptionalParams[] = array(self::KEY_NAME => $inParamName,
            self::KEY_DESCRIPTION => $inDescription);
        return $this;
    }

    /**
     * Add an output field for this procedure.
     *
     * @param string $inFieldName Name of the field.
     * @param string|null $inDescription Description of the field.
     * @return $this
     */
    function addOutputField($inFieldName, $inDescription=null) {
        $this->__outputFields[] = array(self::KEY_NAME => $inFieldName,
            self::KEY_DESCRIPTION => $inDescription);
        return $this;
    }

    /**
     * Add an "output value" to the procedure's description.
     * Please note that an output value is a value that is calculated by the procedure.
     * An output value is not returned by the SGBDR. Values returned by the SGBDR are called "data values".
     *
     * @param string $inValueName Name of the value.
     * @param string|null $inValueDescription Description of the value.
     * @return $this
     */
    public function addOutputValue($inValueName, $inValueDescription=null) {
        $this->__outputValues[] = [ self::KEY_NAME => $inValueName,
            self::KEY_DESCRIPTION => $inValueDescription];
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // User' setters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set SQL requests for this procedure.
     *
     * @param array $inRequests List of requests.
     * @return $this
     */
    public function setRequests(array $inRequests) {
        $this->__requests = array_merge($this->__requests, $inRequests);
        return $this;
    }

    /**
     * Set the list of mandatory fields for this procedure.
     * Please see the comment for the property $__inMandatoryFields.
     *
     * @param array $inFields List of fields.
     *        Each element of this list is an array that specifies one field.
     *        Field' specification:
     *           * First element (mandatory): the name of the field.
     *           * Second element (optional): the description of the field (set it to the value null if you do not want to set any description).
     *           * Third element (optional): is the field always mandatory? (default value if true).
     * @return $this
     * @throws \Exception
     * @see $__inMandatoryFields
     */
    public function setMandatoryInputFields(array $inFields) {
        foreach ($inFields as $_field) {
            /* var array $_field */
            if (0 == count($_field)) {
                throw new \Exception("Invalid specification for mandatory field. You passed an empty description. Valid description is [<name>, <description>?, <mandatory level>?]");
            }
            $name = $_field[0];
            $description = count($_field) > 1 ? $_field[1] : null;
            $always = count($_field) > 2 ? $_field[2] : true;
            $this->addMandatoryInputField($name, $description, $always);
        }
        return $this;
    }

    /**
     * Set the list of optional fields for this procedure.
     * Please see the comment for the property $__inOptionalFields.
     *
     * @param array $inFields List of fields.
     *        Each element of this list is an array that specifies one field.
     *        Field' specification:
     *           * First element (mandatory): the name of the field.
     *           * Second element (optional): the description of the field (default value is null).
     *
     * @return $this
     * @throws \Exception
     * @see $__inOptionalFields
     */
    public function setOptionalInputFields(array $inFields) {
        foreach ($inFields as $_field) {
            /* var array $_field */
            if (0 == count($_field)) {
                throw new \Exception("Invalid specification for optional field. You passed an empty description. Valid description is [<name>, <description>]");
            }
            $name = $_field[0];
            $description = count($_field) > 1 ? $_field[1] : null;
            $this->addOptionalInputField($name, $description);
        }
        return $this;
    }

    /**
     * Set the list of mandatory input parameters for this procedure.
     * Please see the comment for the property $__inMandatoryParams.
     *
     * @param array $inParams List of parameters.
     *        Each element of this list is an array that specifies one parameter.
     *        Parameter' specification:
     *           * First element (mandatory): the name of the parameter.
     *           * Second element (optional): the description of the parameter (set it to the value null if you do not want to set any description).
     *           * Third element (optional): is the parameter always mandatory? (default value if true).
     * @return $this
     * @throws \Exception
     * @see $__inMandatoryParams
     */
    public function setMandatoryInputParams(array $inParams) {
        foreach ($inParams as $_param) {
            /* var array $_param */
            if (0 == count($_param)) {
                throw new \Exception("Invalid specification for mandatory parameter. You passed an empty description. Valid description is [<name>, <description>?, <mandatory level>?]");
            }
            $name = $_param[0];
            $description = count($_param) > 1 ? $_param[1] : null;
            $always = count($_param) > 2 ? $_param[2] : true;
            $this->addMandatoryInputParam($name, $description, $always);
        }
        return $this;
    }

    /**
     * Set the list of optional input parameters for this procedure.
     * Please see the comment for the property $__inOptionalParams.
     *
     * @param array $inParams List of parameters.
     *        Each element of this list is an array that specifies one parameter.
     *        Field' specification:
     *           * First element (mandatory): the name of the parameter.
     *           * Second element (optional): the description of the parameter (default value is null).
     * @return $this
     * @throws \Exception
     * @see $__inOptionalParams
     */
    public function setOptionalInputParams(array $inParams) {
        foreach ($inParams as $_params) {
            /* var array $_field */
            if (0 == count($_params)) {
                throw new \Exception("Invalid specification for optional paramter. You passed an empty description. Valid description is [<name>, <description>]");
            }
            $name = $_params[0];
            $description = count($_params) > 1 ? $_params[1] : null;
            $this->addOptionalInputParam($name, $description);
        }
        return $this;
    }

    /**
     * This method sets the flag that defines whether the procedure returns a list of rows or not.
     *
     * @param bool $inMulti If true: the procedure returns more that one row.
     *        Otherwise: the procedure returns only one row.
     * @return $this
     */
    function setOutputIsMulti($inMulti=true) {
        $this->__outIsMultiRow = $inMulti;
        return $this;
    }

    /**
     * Set the list of output database's fields.
     * @param array $inFields This array contains the list of fields' descriptions.
     *        Each element of this array is an array that contains one or two element.
     *        * First element: the name of the field ("<table.name>").
     *        * Second element: the description of the field. This element is optional.
     * @throws \Exception
     * @see $__outputFields
     */
    function setOutputFields(array $inFields) {
        foreach ($inFields as $_field) {
            if (! is_array($_field)) {
                throw new \Exception("Invalid output field's description. It should be an array.");
            }
            if (0 === count($_field)) {
                throw new \Exception("Invalid output field's description. It should, at least contain the field's name.");
            }
            $name = $_field[0];
            $description = count($_field) > 1 ? $_field[1] : null;

            $this->addOutputField($name, $description);
        }
    }

    /**
     * Set the list of output values.
     * @param array $inValues This array contains the list of values' descriptions.
     *        Each element of this array is an array that contains one or two element.
     *        * First element: the name of the value.
     *        * Second element: the description of the value. This element is optional.
     * @throws \Exception
     * @see $__outputValues
     */
    function setOutputValues(array $inValues) {
        foreach ($inValues as $_value) {
            if (! is_array($_value)) {
                throw new \Exception("Invalid output value's description. It should be an array.");
            }
            if (0 === count($_value)) {
                throw new \Exception("Invalid output value's description. It should, at least contain the value's name.");
            }
            $name = $_value[0];
            $description = count($_value) > 1 ? $_value[1] : null;

            $this->addOutputValue($name, $description);
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEV's testers.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Tell if the procedure returns more than one row.
     *
     * @return bool If the procedure returns more that one row, then the method returns the value true.
     *         Otherwise, the method returns the value false.
     */
    function isOutputMulti_() {
        return $this->__outIsMultiRow;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEV's getters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Get the list of SQL requests used by this procedure.
     * @return array Return the list of SQL requests used by this procedure.
     *         Each element of the list is a string that represents the name of an SQL request.
     */
    public function getRequests_() {
        return $this->__requests;
    }

    /**
     * Return the list of mandatory input fields for this procedure.
     *
     * @return array The method returns the list of mandatory input fields for this procedure.
     *         Each element of the list is an associative array that presents three keys:
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME: the name of the field.
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION: the description.
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_ALWAYS: this value indicated whether the field is always mandatory or not.
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_ALWAYS
     */
    function getMandatoryInputFields_() {
        return $this->__inMandatoryFields;
    }

    /**
     * Return the list of optional input fields for this procedure.
     *
     * @return array The method returns the list of optional input fields for this procedure.
     *         Each element of the list is an associative array that presents two keys:
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME: the name of the field.
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION: the description.
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION
     */
    function getOptionalInputFields() {
        return $this->__inOptionalFields;
    }

    /**
     * Return the list of mandatory input parameters for this procedure.
     *
     * @return array The method returns the list of mandatory input parameters for this procedure.
     *         Each element of the list is an associative array that presents three keys:
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME: the name of the parameter.
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION: the description.
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_ALWAYS: this value indicated whether the parameter is always mandatory or not.
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_ALWAYS
     */
    function getMandatoryInputParams_() {
        return $this->__inMandatoryParams;
    }

    /**
     * Return the list of optional input parameters for this procedure.
     *
     * @return array The method returns the list of optional input parameters for this procedure.
     *         Each element of the list is an associative array that presents two keys:
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME: the name of the parameter.
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION: the description.
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION
     */
    function getOptionalInputParams_() {
        return $this->__inOptionalParams;
    }

    /**
     * Return the list of output fields for this procedure.
     *
     * @return array The method returns the list of output fields for this procedure.
     *         Each element of the list is an associative array that presents two keys:
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME: the name of the parameter.
     *         \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION: the description.
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION
     */
    function getOutputFields_() {
        return $this->__outputFields;
    }

    /**
     * Return the list of "output values" returned by this procedure.
     *
     * @return array The method returns the list of "output values" for this procedure.
     *         Please not that each element of the returned list is an associative array that presents the following keys:
     *         \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription::KEY_NAME: the name of the output value.
     *         \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription::KEY_DESCRIPTION: the description for the output value.
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_NAME
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Procedure::KEY_DESCRIPTION
     */
    public function getOutputValues_() {
        return $this->__outputValues;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // See base class.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     * @see \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription
     */
    protected function _checkFields(array &$inFields, &$outError) {

        if (is_null(self::$_allDbFields)) {
            throw new \Exception("The list of all fields in the database is not set. Please call the static method \"setDbFields()\" first.");
        }

        $checked = [];
        $outError = null;

        /* @var array $_field */
        foreach ($inFields as $_field) {
            $fieldFullName = $_field[self::KEY_NAME];
            $description = $_field[self::KEY_DESCRIPTION];

            $matches = [];
            if (! preg_match('/^([^\.]+)\.([^\.]+)$/', $fieldFullName, $matches)) {
                $outError = "Invalid field name \"${fieldFullName}\" (fields' names must follow the syntax \"table.name\" or \"table.*\").";
                return false;
            }
            $tableName = $matches[1];
            $fieldName = $matches[2];

            if (! array_key_exists($tableName, self::$_fieldsByTable)) {
                $outError = "Invalid field name \"${fieldFullName}\" (table \"${tableName}\" does not exist). Please check your procedure declarations.";
                return false;
            }

            if ('*' === $fieldName) {

                /* @var \dbeurive\Backend\Database\EntryPoints\Description\Element\Field $_fieldDef */
                foreach (self::$_fieldsByTable[$tableName] as $_fieldDef) {
                    $checked[] = [
                        self::KEY_NAME => $_fieldDef->getName(),
                        self::KEY_DESCRIPTION => $description
                    ];
                }
                continue;
            }
            $checked[] = $_field;
        }

        $inFields = $checked;
        return true;
    }

    /**
     * {@inheritdoc}
     * @see \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription
     */
    protected function _getDbFields() {
        return array_unique(array_merge($this->__inMandatoryFields, $this->__inOptionalFields, $this->__outputFields));
    }

    /**
     * {@inheritdoc}
     * @see \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription
     */
    protected function _getAllDdFieldsListsReferences() {
        return [&$this->__inMandatoryFields, &$this->__inOptionalFields, &$this->__outputFields];
    }

    /**
     * {@inheritdoc}
     * @see dbeurive\Backend\Database\Doc\InterfaceDescription
     */
    public function asArray() {
        return [
            'description' => $this->getDescription_(),
            'requests' => $this->__requests,
            'name' => $this->getName_(),
            'tags' => $this->getTags_(),
            'mandatoryInputFields' => $this->__inMandatoryFields,
            'optionalInputFields' => $this->__inOptionalFields,
            'mandatoryInputParams' => $this->__inMandatoryParams,
            'optionalInputParams' => $this->__inOptionalParams,
            'outputFields' => $this->__outputFields,
            'outputValues' => $this->getOutputDataValues_(),
            'entities' => $this->getEntitiesActions_(),
            'multiRow' => $this->__outIsMultiRow
        ];
    }
}