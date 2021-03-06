# Introduction

This directory contains the *documentation writer* component. This component performs the following actions:

  * Extract information from all the entry points (SQL requests and procedures).
  * Organise the information previously extracted (into a SQLite database).

# Configuration

The configuration for this component an associative array with the following keys: 

| Parameter                 | Description                                                                                       |
|---------------------------|---------------------------------------------------------------------------------------------------|
| sql-repository-path       | Absolute path to the base directory use to store all SQL requests (PHP) classes.                  |
| procedure-repository-path | Absolute path to the base directory use to store all procedures (PHP) classes.                    |
| sql-base-namespace        | Base namespace for all SQL requests.                                                              |
| procedure-base-namespace  | Base namespace for all procedures.                                                                |
| schema-path               | Path to the PHP file that contains the schema of the database (the list of all fields). See note. |
| doc-path                  | Path to the SQLite database that will be created, and that contains the generated documentation.  |

Note: 

> The PHP file that contains the schema of the database has been previously generated by the script `backend`.
  See this [example](https://github.com/dbeurive/backend/blob/master/tests/cache/mysql_schema.php).
> See [this documentation](https://github.com/dbeurive/backend/blob/master/src/Cli/Bin/README.md).

See:

* [Parameters that define the organisation of entry points](https://github.com/dbeurive/backend/blob/master/src/Database/EntryPoints/ConfigurationParameter.php) (SQL request or procedure).
* [Parameters that define the organisation of the generated documentation](https://github.com/dbeurive/backend/blob/master/src/Database/Doc/ConfigurationParameter.php) (the generated documentation).

# Generating the documentation from the command line

See [this document](https://github.com/dbeurive/backend/blob/master/src/Cli/Bin/README.md).

