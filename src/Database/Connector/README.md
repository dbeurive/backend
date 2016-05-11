# Description

This namespace implements all "connectors".

Connectors encapsulate a low-level database handler, such as PDO ot mysqli.
This low-level database handler is passed to the API's entry points, so they can use it directly.

And because all low-level database handlers don't have a standardised API, the connector's API encapsulates these functionalities, so the underlying software layer does not rely on a specific database handler API.

As an example, to quote a value:

   * Using `PDO`: `PDO::quote()`
   * Using `mysqli`: `mysqli::escape_string()`
   
This functionality is implemented as `AbstractConnector::quoteValue()`.

