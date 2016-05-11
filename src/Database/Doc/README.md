# Description

This directory contains the *documentation writer* component. This component performs the following actions:

  * Extract information from all the API's entry points (SQL requests and procedures).
  * Organize the information previously extracted (into an SQLite database).

# Configuration

The configuration for this component an associative array with the following keys: 

| Parameter                 | Description                                                                                   |
|---------------------------|-----------------------------------------------------------------------------------------------|
| sql-repository-path       | Absolute path to the base directory use to store all SQL requests (PHP) classes.              |
| procedure-repository-path | Absolute path to the base directory use to store all procedures (PHP) classes.                |
| sql-base-namespace        | Base namespace for all SQL requests.                                                          |
| procedure-base-namespace  | Base namespace for all procedures.                                                            |
| schema-path               | Path to the PHP file that contains the schema of the database (the list of all fields).       |
| db-connector-class-name   | Fully qualified name of the class the implements the connector used to access the database.   | 

See:

* [Parameters that define the organisation of API's entry points](https://github.com/dbeurive/backend/blob/master/src/Database/Doc/Option.php)
* [Parameters that define the organisation of the generated documentation](https://github.com/dbeurive/backend/blob/master/src/Database/Doc/Option.php)

