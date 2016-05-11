# Description

This namespace implements all "connectors".

Connectors encapsulate a low-level database handler, such as PDO ot mysqli.
This low-level database handler is passed to the API's entry points, so they can use it directly.

Connectors provide functionalities that are specific to a given brand of database server, but that is not provided by the low level database handler.
Right now, there is only on such functionality: quoting fully qualified fields' names.
For example, have a look the the static method [`quoteFieldName()`](https://github.com/dbeurive/backend/blob/master/src/Database/Connector/MySql.php).

> Please note that, because these functionalities does not require an open connexion to the database, they are implemented as static methods.
> These functionalities can be used without configuring the connector first. See the interface [`\dbeurive\Backend\Database\Connector\InterfaceConnector`](https://github.com/dbeurive/backend/blob/master/src/Database/Connector/InterfaceConnector.php).

And because all low-level database handlers don't have a standardised API, the connector's API encapsulates these functionalities, so the underlying software layer does not rely on a specific database handler API.

As an example, to quote a value:

   * Using `PDO`: `PDO::quote()`
   * Using `mysqli`: `mysqli::escape_string()`
   
This functionality is implemented as `AbstractConnector::quoteValue()`.

