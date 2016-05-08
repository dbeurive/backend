# Description

This namespace defines a *database link*.

A *database link* holds a low level database connexion handler (such as PDO) along with specific methods.

These specific methods:

* Extends the functionalities of the low level database connexion handler.
  * `function quoteValue($inValue)`
  * `function quoteFieldName($inFieldName)`
  * `function getDatabaseSchema()`
  * `function getErrorCode()`
  * `function getErrorMessage()`
* Allows transparent integration of the low level database connexion handler within the main configuration.
  * `function getConfigurationOptions()`

