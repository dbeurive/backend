# Description

This namespace contains the implementations for all "schema extractors".

Schema extractors are software components that return all the fields of a given database.

There is one schema extractor for each brand of database (MySql, Oracle...).

Adding support for a new brand of database involves adding a new class that extends the base class
[`\dbeurive\Backend\Database\SchemaExtractor\AbstractSchemaExtractor`](https://github.com/dbeurive/backend/blob/master/src/Database/SchemaExtractor/AbstractSchemaExtractor.php) and
implements the interface [\dbeurive\Backend\Database\SchemaExtractor\InterfaceExtractor](https://github.com/dbeurive/backend/blob/master/src/Database/SchemaExtractor/InterfaceExtractor.php).


