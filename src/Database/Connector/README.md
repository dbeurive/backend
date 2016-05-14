# Description

This namespace implements all "connectors".

Connectors are just very thin wrappers around a low-level database handler, such as PDO or mysqli.
A connector performs the following actions:
 
* It exports the configuration's parameters required to initialise the low-level database handler.
  See AbstractConnector::getConfigurationParameters
* It initialises the connexion to the database through the low-level database handler.
  See AbstractConnector::connect

In other words:

* You can use PDO to access MySql or SQLite databases.
  However, the configuration's parameters required to initialise a connexion to a MySql server are not the same than the ones required to open a SQLite database.
* The APIs used to open a connexion to the database for PDO and mysqli differ. For example:
  * PDO: `$dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass);`.
  * mysqli: `$link = mysqli_connect('localhost', 'my_user', 'my_password', 'my_db');` 
  
The purpose of the connectors is to export a unified API for all low-level database handlers. Basically: `getConfigurationParameters()` and `connect()`.
With this basic API:

* The application knows what configurations' parameters it should get.
* The application is able to open a connexion to the database using the right set of parameters.

