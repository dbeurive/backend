# Description

This namespace implements all "database connectors".

Database connectors are plugins for applications.
They expose an API that allows the application to get an instance a low-level database handler, such as PDO or mysqli. 

A database connector implements code for:

* A given brand of database server (example: MySql, Oracle...).
* A given low-level database handler (example: PDO or mysqli).

See [an example](https://github.com/dbeurive/backend/blob/master/src/Cli/Adapter/Database/Connector/MySqlPdo.php) of database connector.

Connectors' API is:

  * `getConfigurationParameters()` (static)
  * `checkConfiguration(array $inConfiguration)` (static)
  * `__construct(array $inOptConfiguration, $inOtpConnect=false)`
  * `connect()`
 
See [AbstractConnector](https://github.com/dbeurive/backend/blob/master/src/Cli/Adapter/Database/Connector/AbstractConnector.php).

As the name implies, a connector is responsible for establishing the connexion to the database.

In order to establish the connexion, the application that uses the connector needs to know the list of required parameters (used by the wrapped low-level database handler).
This information is given by the connector to the application through the (static) method `getConfigurationParameters()`.

Once the application gets the list of required parameters, it should present the user the list of parameters to specify (through the command line interface).

Then, once the parameters get specified, then the application should validate them. This is the purpose of the (static) method `checkConfiguration()`.

If the list of parameters is valid, the application can create the connector and establish the connection to the database (using the method `connect()`). 

Adding a connector for a new couple (database's brand, low-level database handler) involves adding a new class that extends the base class [`\dbeurive\Backend\Cli\Adapter\Database\Connector\AbstractConnector`](https://github.com/dbeurive/backend/blob/master/src/Cli/Adapter/Database/Connector/AbstractConnector.php)
and implements the interface [`\dbeurive\Backend\Cli\Adapter\Database\Connector\InterfaceConnector`](https://github.com/dbeurive/backend/blob/master/src/Cli/Adapter/Database/Connector/InterfaceConnector.php).   


