# Introduction

Although ORM exist, using SQL is a valid strategy.
And one should not start using an ORM just because it is the recommended choice, or because it is part of the default framework’s distribution.
In many ways, plain good old SQL is a pragmatic choice.

This project is an attempt to make using SQL an even more pragmatic approach by providing a simple way to organise and to document SQL.

Note 1:

> Please note that, in this document, the term _procedure_ refers to a function, written in PHP, that uses one or more SQL requests.
> You can draw an analogy with the concept of _stored procedures_, except that _procedures_ execute on the database’s client (rather than on the database itself).

Note 2:

> Please also note that the term "database entry point" refers to an SQL request or a procedure.






# Overview

## You had a dream

Wouldn’t it be nice to ask something like:

> * Give me all the SQL requests or procedures that select this field.
> * Give me all the SQL requests or procedures that select these fields.
> * Give me all the SQL requests or procedures that update these fields.
> * Give me all the SQL requests or procedures that return this quantity (for example: the number of users).
> * Give me all the SQL requests or procedures that are associated with this tag, and that select these fields.
> * ...

Wouldn’t it be nice if your IDE could immediately locate SQL requests or procedures within the entire application's code with 100% accuracy ?

Sure, that would be nice !

You could instantly find where, in your code, a specific action on some data is done.

If you modify your database's schema, you could find out very quickly, and very accurately, the impacts on the code. 

If you are new to the application, having a very flexible documentary referential should be a great help.  

## How to make the dream come true?

In order to make your first wish come true, you need to build a relational database that organises all the information about SQL requests and procedures.
Once you've injected all the information about SQL requests and procedures into a relational database, then you can use SQL to express your requests for information. 

In order to make your second wish come true, you need to identify SQL requests and procedures into PHP constructs that can be identified by your IDE.
[Constants](http://php.net/manual/fr/language.constants.php) or [classes’ constants](http://php.net/manual/fr/language.oop5.constants.php) are perfectly suited for this use.

In practice:

* _Database entry points_ (SQL requests and procedures) are (PHP) classes which fully qualified names and paths follow the [PSR4 specification](http://www.php-fig.org/psr/psr-4/).
* Classes that implements database entry points implement an interface that is used to extract information about the database entry points. 
  This information will be injected into a documentary relational database.
* The [fully qualified name](https://en.wikipedia.org/wiki/Fully_qualified_name) (FQN) of the class that implements a _database entry points_ is a [URI](https://en.wikipedia.org/wiki/Uniform_Resource_Identifier).
* All fully qualified names of the classes that implement _database entry points_ associated to a given database must share a common [namespace](http://php.net/manual/en/language.namespaces.php).
  Given the previous constraints, this implies that all the file that implements the classes share a common (prefix) path.  
* Rather than using the fully qualified names as [URI](https://en.wikipedia.org/wiki/Uniform_Resource_Identifier), we use only the last part of them.
  Because all _database entry points_ fully qualified names share the same prefix, we use the "variable part" (of the FQN) to identify a _database entry point_.
  We call this "variable part" the (unique) "name" of the _database entry point_. 

For example:

We assume that your file `composer.json` contains the following specification: 

```json
  "autoload": {
    "psr-4": {
      "dbeurive\\Application\\": "src"
    }
  },
```

And that we have the following file tree:

    src
    ├── Procedures
    │   └── User
    │       ├── Authenticate.php
    │       └── Delete.php
    └── Sqls
        ├── Profile
        │   └── Get.php
        └── User
            ├── Authenticate.php
            └── Delete.php

> Please note that all procedures are stored under the directory `src/Procedures`. And all SQL request are stored under the directory `src/Sqls`.

Then, the names for the SQL requests and the procedures are:

| File                                 | FQN of the class                                   | Unique name       |
|--------------------------------------|----------------------------------------------------|-------------------|
| src/Procedures/User/Authenticate.php | \dbeurive\Application\Procedures\User\Authenticate | User\Authenticate |
| src/Procedures/User/Delete.php       | \dbeurive\Application\Procedures\User\Delete       | User\Delete       |
| src/Sqls/Profile/Get.php             | \dbeurive\Application\Sqls\Profile\Get             | Profile\Get       |
| src/Sqls/User/Authenticate.php       | \dbeurive\Application\Sqls\User\Authenticate       | User\Authenticate |
| src/Sqls/User/Delete.php             | \dbeurive\Application\Sqls\User\Delete             | User\Delete       |

> Please note that, although an SQL request with the same name exists, "`User\Authenticate`" is a unique name _for a procedure_.

Then, a script scans all the classes for SQL requests and procedures. As a result, this script produces an SQLite database that organises all information extracted from the scanned classes.

The generated SQLite database is a _structured documentary database_. And we can use simple SQL to query it.

> **Your first wish is fulfilled.**

And if you define databases entry points' names as [constants](http://php.net/manual/fr/language.constants.php) (or [classes’ constants](http://php.net/manual/fr/language.oop5.constants.php)), then you can use your IDE to find any use of an entry point within your entire application, with 100% accuracy.
 
Using the previous example, we should define the following identifiers:

```php
define('SQL_USER_AUTHENTICATION', 'User\\Authenticate');
define('SQL_USER_DELETE', 'User\\Delete');
define('SQL_PROFILE_GET', 'Profile\\Get');

define('PROCEDURE_USER_AUTHENTICATION', 'User\\Authenticate');
define('PROCEDURE_USER_DELETE', 'User\\Delete');
```

Or something like:

```php
Class SqlId {
    const SQL_USER_AUTHENTICATION = 'User\\Authenticate';
    const SQL_USER_DELETE = 'User\\Delete';
    const SQL_PROFILE_GET = 'Profile\\Get';
};

Class ProcedureId {
    const PROCEDURE_USER_AUTHENTICATION = 'User\\Authenticate';
    const PROCEDURE_USER_DELETE = 'User\\Delete';
};
```

> **Your second wish is fulfilled.**




# Installation

From the command line:

    composer require dbeurive\backend
    
If you want to include this package to your project, then edit your file `composer.json` and add the following entry:

```json
  "require": {
    "dbeurive/backend": "*"
  }
```


 
 




 