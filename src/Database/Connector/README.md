# Description

This namespace implements all "database connectors".

Database connectors are just very thin wrappers around a low-level database handler, such as PDO or mysqli.

Connectors' API is:

  * `getConfigurationParameters()` (static)
  * `checkConfiguration(array $inConfiguration)` (static)
  * `__construct(array $inOptConfiguration, $inOtpConnect=false)`
  * `connect()`
 

As the name implies, a connector is responsible for establishing the connexion to the database.

In order to establish the connexion, the application that uses the connector needs to know the list of required parameters (used by the wrapped low-level database handler).
This information is given by the connector to the application through the (static) method `getConfigurationParameters()`.

Once the application gets the list of required parameters, it should present the user the list of parameters to specify.

Then, once the parameters get specified, then the application should validate them. This is the purpose of the (static) method `checkConfiguration()`.

If the list of parameters is valid, the application can create the connector and establish the connection to the database (using the method `connect()`). 




