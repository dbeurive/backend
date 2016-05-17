<?php

/**
 * This file implements the class that represents the description for an SQL request.
 */

namespace dbeurive\Backend\Database\EntryPoints\Description;

/**
 * Class Sql
 *
 * This class contains all the information that describes an SQL request.
 *
 * @package dbeurive\Backend\Database\EntryPoints\Description
 */

class Sql extends \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription
{
    const KEY_NAME        = 'name';
    const KEY_DESCRIPTION = 'description';

    /**
     * This request performs a selection.
     */
    const TYPE_SELECT = 'select';
    /**
     * This request updates records.
     */
    const TYPE_UPDATE = 'update';
    /**
     * his request deletes records.
     */
    const TYPE_DELETE = 'delete';
    /**
     * This request inserts reqords.
     */
    const TYPE_INSERT = 'insert';
    /**
     * This request updates a record, or inserts a new record, depending on the context.
     */
    const TYPE_UPSERT = 'upsert';


    /**
     * @var array List of tables used by this request.
     */
    private $__tables = [];
    /**
     * @var array List of selected tables' fields.
     */
    private $__selectedFields = [];
    /**
     * @var array List of updated tables' fields.
     */
    private $__updatedFields = [];
    /**
     * @var array List of tables' fields used in the WHERE clause.
     */
    private $__conditionFields = [];
    /**
     * @var array List of inserted tables' fields.
     */
    private $__insertedFields = [];
    /**
     * @var array List of "upserted" tables' fields.
     */
    private $__upsertedFields = [];
    /**
     * @var array List of tables' fields used to structure a selection (ORDER BY, GROUP BY...).
     */
    private $__presentationFields = [];
    /**
     * @var array List of values used to configure the request (ex: LIMIT...).
     */
    private $__parameterValues = [];
    /**
     * @var string|array The QL request.
     *      Please note that you are free to choose your own formalism. Plain text should be enough.
     *      But in rare occasions, you may want to use another formalism. In this case, you should give an array.
     *      The content of the array does not matter (you can put anything you want inside), as long as you provide a software component to interpret its content.
     */
    private $__sql = null;
    /**
     * @var string Type of the request.
     *      It can be: \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_SELECT,
     *                 \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPDATE,
     *                 \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_DELETE,
     *                 \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_INSERT,
     *                 \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPSERT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_SELECT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPDATE
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_DELETE
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_INSERT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPSERT
     */
    private $__type = null;

    // -----------------------------------------------------------------------------------------------------------------
    // User's adders.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Add a table to the description.
     * @param string $inTable Name of the table to add to the description.
     * @return $this
     */
    public function addTable($inTable) {
        $this->__tables[] = $inTable;
        return $this;
    }

    /**
     * Add a field to the list of fields selected by this request.
     * This method may also be used to specify that the request selects all fields from a given table.
     * @param string $inField Field to add.
     *        This is a string "<table name>.<field name>" or "<table name>.*".
     * @return $this
     */
    public function addSelectedField($inField) {
        $this->__selectedFields[] = $inField;
        return $this;
    }

    /**
     * Add a field to the list fields updated by the request.
     * This method may also be used to specify that the request updates all fields from a given table.
     * @param string $inField Field to add.
     *        This is a string "<table name>.<field name>" or "<table name>.*".
     * @return $this
     */
    public function addUpdatedField($inField) {
        $this->__updatedFields[] = $inField;
        return $this;
    }

    /**
     * Add a field to the list of fields used in the WHERE clause, for this request.
     * This method may also be used to specify that the request uses all fields from a given table in the WHERE clause.
     * @param string $inField Field to add.
     *        This is a string "<table name>.<field name>" or "<table name>.*".
     * @return $this
     */
    public function addConditionField($inField) {
        $this->__conditionFields[] = $inField;
        return $this;
    }

    /**
     * Add a field to the list fields inserted by the request.
     * This method may also be used to specify that the request inserts all fields from a given table.
     * @param string $inField Field to insert.
     *        This is a string "<table name>.<field name>" or "<table name>.*".
     * @return $this
     */
    public function addInsertedField($inField) {
        $this->__insertedFields[] = $inField;
        return $this;
    }

    /**
     * Add a field to the list fields "upserted" by the request.
     * This method may also be used to specify that the request "upserts" all fields from a given table.
     * @param string $inField Field to insert.
     *        This is a string "<table name>.<field name>" or "<table name>.*".
     * @return $this
     */
    public function addUpsertedField($inField) {
        $this->__upsertedFields = $inField;
        return $this;
    }

    /**
     * Add a field to the list of fields used to organize the request's selection.
     * This method may also be used to specify that the request uses all fields from a given table in order to organise the selection.
     * @param string $inField Field to add.
     *        This is a string "<table name>.<field name>" or "<table name>.*".
     * @return $this
     */
    public function addPresentationField($inField) {
        $this->__presentationFields[] = $inField;
        return $this;
    }

    /**
     * Add a parameter's value to the list of parameters used to configure the request.
     * @param string $inValue Parameter's value to add.
     * @param string $inOptDescription Parameter's description.
     * @return $this
     */
    public function addParameterValue($inValue, $inOptDescription=null) {
        $this->__parameterValues[] = [ self::KEY_NAME => $inValue, self::KEY_DESCRIPTION => $inOptDescription ];
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // User' setters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Set the SQL request type.
     * @param string $inType Type of the request. The value can be:
     *        \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_DELETE
     *        \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_INSERT
     *        \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_SELECT
     *        \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPDATE
     *        \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPSERT
     * @return $this
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_DELETE
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_INSERT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_SELECT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPDATE
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPSERT
     */
    public function setType($inType) {
        $allowed =[self::TYPE_DELETE, self::TYPE_INSERT, self::TYPE_SELECT, self::TYPE_UPDATE, self::TYPE_UPSERT];
        $inType = strtolower($inType);
        if (! in_array($inType, $allowed)) {
            throw new \Exception("Request's type \"$inType\" is not allowed. Allowed types are: " . implode(', ', $allowed));
        }
        $this->__type = $inType;
        return $this;
    }

    /**
     * Set the SQL request(s) for this request.
     * @param string|array $inSql SQL request(s) used by this request.
     *        Please note that you are free to choose your own formalism. Plain text should be enough.
     *        But in rare occasions, you may want to use another formalism. In this case, you should give an array.
     *        The content of the array does not matter (you can put anything you want inside), as long as you provide a software component to interpret its content.
     * @return $this
     */
    public function setSql($inSql) {
        $this->__sql = $inSql;
        return $this;
    }

    /**
     * Set the list of tables for this requests.
     * @param array $inTables List of tables' names.
     * @return $this
     */
    public function setTables(array $inTables) {
        $this->__tables = $inTables;
        return $this;
    }

    /**
     * Set the list of selected fields for this request.
     * @param array $inFields list of fields' names. The name is a string that respects the following syntax:
     *              "<table table>.<field name>" or "<table table>.*"
     * @return $this
     */
    public function setSelectedFields(array $inFields) {
        $this->__selectedFields = $inFields;
        return $this;
    }

    /**
     * Set the list of updated fields for this request.
     * @param array $inFields list of fields' names. The name is a string that respects the following syntax:
     *              "<table table>.<field name>" or "<table table>.*"
     * @return $this
     */
    public function setUpdatedFields(array $inFields) {
        $this->__updatedFields = $inFields;
        return $this;
    }

    /**
     * Set the list of inserted fields for this request.
     * @param array $inFields list of fields' names. The name is a string that respects the following syntax:
     *              "<table table>.<field name>" or "<table table>.*"
     * @return $this
     */
    public function setInsertedFields(array $inFields) {
        $this->__insertedFields = $inFields;
        return $this;
    }

    /**
     * Set the list of "upserted" fields for this request.
     * @param array $inFields list of fields' names. The name is a string that respects the following syntax:
     *              "<table table>.<field name>" or "<table table>.*"
     * @return $this
     */
    public function setUpsertedFields(array $inFields) {
        $this->__upsertedFields = $inFields;
        return $this;
    }

    /**
     * Set the list of fields used in the WHERE clause.
     * @param array $inFields list of fields' names. The name is a string that respects the following syntax:
     *              "<table table>.<field name>" or "<table table>.*"
     * @return $this
     */
    public function setConditionFields(array $inFields) {
        $this->__conditionFields = $inFields;
        return $this;
    }

    /**
     * Set the list of fields used to organize the request's selection.
     * @param array $inFields list of fields' names. The name is a string that respects the following syntax:
     *              "<table table>.<field name>" or "<table table>.*"
     * @return $this
     */
    public function setPresentationFields(array $inFields) {
        $this->__presentationFields = $inFields;
        return $this;
    }

    /**
     * Set the list of parameters' values used to configure the request.
     * @param array $inValues List of parameters' values. Each element of the given array must be an array that contains one or two elements:
     *        * The first element represents the name of the parameter.
     *        * The second element represents the description of the parameter (default value is null).
     * @return $this
     * @throws \Exception
     * @see $__parameterValues
     */
    public function setParameterValues(array $inValues) {
        /** @var array $_value */
        foreach ($inValues as $_value) {
            if (count($_value) == 0) {
                throw new \Exception("Invalid specification for parameter. You passed an empty description. Valid description is [<name>, <description>?]");
            }
            $name = $_value[0];
            $description = count($_value) > 1 ? $_value[1] : null;
            $this->addParameterValue($name, $description);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEV's getters.
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return the type of SQL request.
     * @return string This method returns the type of the SQL request.
     *         The value can be:
     *         \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_DELETE
     *         \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_INSERT
     *         \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_SELECT
     *         \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPDATE
     *         \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPSERT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_DELETE
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_INSERT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_SELECT
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPDATE
     * @see \dbeurive\Backend\Database\EntryPoints\Description\Sql::TYPE_UPSERT
     */
    public function getType_() {
        return $this->__type;
    }

    /**
     * Return the SQL request(s) used by this request.
     * @return array|string
     */
    public function getSql_() {
        return $this->__sql;
    }

    /**
     * Return the fields selected by this request.
     * @return array
     */
    public function getSelectedFields_() {
        return $this->__selectedFields;
    }

    /**
     * Return the fields updated by the request.
     * @return array
     */
    public function getUpdatedFields_() {
        return $this->__updatedFields;
    }

    /**
     * Return the fields "upserted" by the request.
     * @return array
     */
    public function getUpsertedFields_() {
        return $this->__upsertedFields;
    }

    /**
     * Return the fields inserted by this request.
     * @return array
     */
    public function getInsertedFields_() {
        return $this->__insertedFields;
    }

    /**
     * Return the list of fields used within the WHERE clause, for this request.
     * @return array
     */
    public function getConditionFields_() {
        return $this->__conditionFields;
    }

    /**
     * Return the list of fields used to organize the selected fields.
     * @return array
     */
    public function getPresentationFields_() {
        return $this->__presentationFields;
    }

    /**
     * Return the list of parameters' values used to configure the request.
     * @return array
     * @see $__parameterValues
     */
    public function getParameterValues_() {
        return $this->__parameterValues;
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

        /* @var string $_field */
        foreach ($inFields as $_field) {
            $matches = [];
            if (! preg_match('/^([^\.]+)\.([^\.]+)$/', $_field, $matches)) {
                $outError = "Invalid field name \"${_field}\" (fields' names must follow the syntax \"table.name\" or \"table.*\").";
                return false;
            }
            $tableName = $matches[1];
            $fieldName = $matches[2];

            if (! array_key_exists($tableName, self::$_fieldsByTable)) {
                $outError = "Invalid field name \"${_field}\" (table \"${tableName}\" does not exist).";
                return false;
            }

            if ('*' === $fieldName) {
                foreach (self::$_fieldsByTable[$tableName] as $_fieldDef) {
                    /* @var \dbeurive\Backend\Database\EntryPoints\Description\Element\Field $_fieldDef */
                    $checked[] = $_fieldDef->getName();
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
        return array_unique(array_merge($this->__conditionFields, $this->__selectedFields, $this->__updatedFields));
    }

    /**
     * {@inheritdoc}
     * @see \dbeurive\Backend\Database\EntryPoints\Description\AbstractDescription
     */
    protected function _getAllDdFieldsListsReferences() {
        return [&$this->__selectedFields, &$this->__conditionFields, &$this->__updatedFields];
    }

    /**
     * {@inheritdoc}
     * @see dbeurive\Backend\Database\Doc\InterfaceDescription
     */
    public function asArray() {
        return [
            'description' => $this->getDescription_(),
            'tables' => $this->__tables,
            'selected' => $this->__selectedFields,
            'inserted' => $this->__insertedFields,
            'upserted' => $this->__upsertedFields,
            'condition' => $this->__conditionFields,
            'presentation' => $this->__presentationFields,
            'parameters' => $this->__parameterValues,
            'name' => $this->getName_(),
            'sql' => $this->__sql,
            'tags' => $this->getTags_(),
            'entities-action relationships' => $this->getEntitiesActions_(),
            'output-data-values' => $this->getOutputDataValues_()
        ];
    }
}