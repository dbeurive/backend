<?php

/**
 * This file defined the schema of the SQLite database that is used to organise all information about API's entry points.
 */

return [

    /* -------------------------------------------------------------------------------------------------------------- */
    /* Simple types.                                                                                                  */
    /* -------------------------------------------------------------------------------------------------------------- */

    // The following table contains the tags.

    'CREATE TABLE tag (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        tag          TEXT NOT NULL
    )',

    'CREATE UNIQUE INDEX index_tag_tag ON tag(tag)',

    // The following table contains the entities.

    'CREATE TABLE entity (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        name         TEXT NOT NULL
    )',

    'CREATE UNIQUE INDEX index_entity_name ON entity(name)',

    // The following table contains the actions between a procedure and an entity.

    'CREATE TABLE action (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        name         TEXT NOT NULL
    )',

    'CREATE UNIQUE INDEX index_action_name ON action(name)',

    'CREATE TABLE "table" (
        id             INTEGER PRIMARY KEY AUTOINCREMENT,
        name           TEXT NOT NULL
    )',

    'CREATE UNIQUE INDEX index_table ON "table" (name)',

    'CREATE TABLE request (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        sql          TEXT NOT NULL,
        multiSql     INTEGER NOT NULL,
        description  TEXT NOT NULL,
        name         TEXT NOT NULL,
        type         TEXT NOT NULL
    )',

    'CREATE UNIQUE INDEX index_request_name ON request (name)',

    'CREATE TABLE procedure (
        id              INTEGER PRIMARY KEY AUTOINCREMENT,
        description     TEXT NOT NULL,
        name            TEXT NOT NULL,
        resultMultiRow  INTEGER NOT NULL
    )',

    'CREATE UNIQUE INDEX index_procedure_name ON procedure (name)',

    /* -------------------------------------------------------------------------------------------------------------- */
    /* Composite types (with foreign keys to simple types).                                                           */
    /* -------------------------------------------------------------------------------------------------------------- */

    'CREATE TABLE field (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        name          TEXT NOT NULL,
        table_id      INTEGER NOT NULL,
        FOREIGN KEY(table_id) REFERENCES "table"(id),
        unique(name, table_id)
    )',

    /* -------------------------------------------------------------------------------------------------------------- */
    /* Relations between SQL requests and other elements.                                                             */
    /* -------------------------------------------------------------------------------------------------------------- */

    'CREATE TABLE requestSelectionField (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id  INTEGER NOT NULL,
        field_id    INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(request_id, field_id)
    )',

    'CREATE TABLE requestUpdateField (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id  INTEGER NOT NULL,
        field_id    INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(request_id, field_id)
    )',

    'CREATE TABLE requestConditionField (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id  INTEGER NOT NULL,
        field_id    INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(request_id, field_id)
    )',

    'CREATE TABLE requestInsertField (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id  INTEGER NOT NULL,
        field_id    INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(request_id, field_id)
    )',

    'CREATE TABLE requestUpsertField (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id  INTEGER NOT NULL,
        field_id    INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(request_id, field_id)
    )',

    'CREATE TABLE requestPresentationField (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id  INTEGER NOT NULL,
        field_id    INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(request_id, field_id)
    )',

    'CREATE TABLE requestParameterValue (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id   INTEGER NOT NULL,
        name         TEXT NOT NULL,
        description  TEXT,
        FOREIGN KEY(request_id) REFERENCES request(id),
        unique(request_id, name)
    )',

    'CREATE INDEX name_idx ON requestParameterValue (name)',

    'CREATE TABLE requestTag (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id  INTEGER NOT NULL,
        tag_id      INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(tag_id) REFERENCES tag(id),
        unique(request_id, tag_id)
    )',

    'CREATE TABLE requestOutputDataValue (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id    INTEGER NOT NULL,
        name          TEXT NOT NULL,
        description   TEXT,
        FOREIGN KEY(request_id) REFERENCES request(id),
        unique(request_id, name)
    )',

    'CREATE TABLE requestEntityAction (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id    INTEGER NOT NULL,
        entity_id     INTEGER NOT NULL,
        action_id     INTEGER NOT NULL,
        FOREIGN KEY(request_id) REFERENCES request(id),
        FOREIGN KEY(entity_id) REFERENCES entity(id),
        FOREIGN KEY(action_id) REFERENCES action(id),
        unique(request_id, entity_id, action_id)
    )',

    /* -------------------------------------------------------------------------------------------------------------- */
    /* Relations between procedures and other elements.                                                               */
    /* -------------------------------------------------------------------------------------------------------------- */

    'CREATE TABLE procedureTag (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        tag_id        INTEGER NOT NULL,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        FOREIGN KEY(tag_id) REFERENCES tag(id),
        unique(procedure_id, tag_id)
    )',

    'CREATE TABLE procedureRequest (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        request_id    INTEGER NOT NULL,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        FOREIGN KEY(request_id) REFERENCES request(id),
        unique(procedure_id, request_id)
    )',

    'CREATE TABLE procedureInputField (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        field_id      INTEGER NOT NULL,
        mandatory     INTEGER NOT NULL,
        description   TEXT,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(procedure_id, field_id)
    )',

    'CREATE TABLE procedureOutputField (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        field_id      INTEGER NOT NULL,
        description   TEXT,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        FOREIGN KEY(field_id) REFERENCES field(id),
        unique(procedure_id, field_id)
    )',

    'CREATE TABLE procedureOutputDataValue (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        name          TEXT NOT NULL,
        description   TEXT,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        unique(procedure_id, name)
    )',

    'CREATE TABLE procedureInputParam (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        name          TEXT NOT NULL,
        description   TEXT,
        mandatory     INTEGER NOT NULL,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        unique(procedure_id, name)
    )',

    'CREATE TABLE procedureOutputValue (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        name          TEXT NOT NULL,
        description   TEXT,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        unique(procedure_id, name)
    )',

    'CREATE TABLE procedureEntityAction (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        procedure_id  INTEGER NOT NULL,
        entity_id     INTEGER NOT NULL,
        action_id     INTEGER NOT NULL,
        FOREIGN KEY(procedure_id) REFERENCES procedure(id),
        FOREIGN KEY(entity_id) REFERENCES entity(id),
        FOREIGN KEY(action_id) REFERENCES action(id),
        unique(procedure_id, entity_id, action_id)
    )'
];