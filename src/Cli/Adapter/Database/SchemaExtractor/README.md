# Introduction

This namespace defines the "schema extractors".

Schema extractors are plugins for applications.
They expose an API that allows the application to get the schema of a given database.

A database connector implements code for a given brand of database (example: MySql, Oracle...).

Extractors' API is:

  * `getConnectorClassName()` (static).
  * __construct()
  
And an extractor for a given brand of database must implement the protected method `_getDatabaseSchema(AbstractConnector $inConnector)`.

See the class [`\dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\AbstractSchemaExtractor`](https://github.com/dbeurive/backend/blob/master/src/Cli/Adapter/Database/SchemaExtractor/AbstractSchemaExtractor.php)
and the interface [`\dbeurive\Backend\Cli\Adapter\Database\SchemaExtractor\InterfaceSchemaExtractor`](https://github.com/dbeurive/backend/blob/master/src/Cli/Adapter/Database/SchemaExtractor/InterfaceSchemaExtractor.php).

