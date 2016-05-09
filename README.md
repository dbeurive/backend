# Introduction

Although ORM exist, using SQL is a valid strategy.
And one should not start using an ORM just because it is the recommended choice, or because it is part of the default framework’s distribution.
In many ways, plain good old SQL is a pragmatic choice.

This project is an attempt to make using SQL an even more pragmatic approach by providing a simple way to organise and to document SQL.

What does this is all about ?

This is all about documentation. Please read: [why did I write this database access layer?](https://github.com/dbeurive/backend/blob/master/doc/WHY.md)
If you access you database through this API, then you can get a SQLite database that organises all information about your SQL requests and your procedures.

* Click [here](https://github.com/dbeurive/backend/blob/master/src/Database/Doc/schema.php) to get the schema for the generated SQLite database.
* You can download an example of generated database [here](https://github.com/dbeurive/backend/blob/master/tests/cache/mysql_db_schema.sqlite).

```sqlite
$ sqlite3 mysql_db_schema.sqlite
sqlite> .schema
sqlite> .mode line
sqlite> .table
sqlite> .schema requestTag
```

Select all SQL requests tagged "authentication":

```sql
SELECT     "request"."name" as "request.name"
FROM       "request" 
INNER JOIN "requestTag" ON "requestTag"."request_id"="request"."id"
INNER JOIN "tag" ON "requestTag"."tag_id"="tag"."id"
WHERE      "tag"."tag"="authentication";
```

Select all SQL requests that select the field "user.id":

```sql
SELECT     "request"."name" as "request.name"
FROM       "request"
INNER JOIN "requestSelectionField" ON "request"."id"="requestSelectionField"."request_id"
INNER JOIN "field" ON "field"."id"="requestSelectionField"."field_id"
WHERE      "field"."name"="user.id";
```

Select all SQL requests tagged "authentication", and that select the field "user.id":

```sql
SELECT     "request"."name" as "request.name"
FROM       "request" 
INNER JOIN "requestTag" ON "requestTag"."request_id"="request"."id"
INNER JOIN "tag" ON "requestTag"."tag_id"="tag"."id"
INNER JOIN "requestSelectionField" ON "request"."id"="requestSelectionField"."request_id"
INNER JOIN "field" ON "field"."id"="requestSelectionField"."field_id"
WHERE      "tag"."tag"="authentication"
  AND      "field"."name"="user.id";
```

...

# Overview

![Generic overview](https://github.com/dbeurive/backend/blob/master/doc/overview.png)

# Contents

* [Installation notes](https://github.com/dbeurive/backend/blob/master/doc/INSTALL.md)
* General description of entry points (SQL requests and procedures)
  * [Writing SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/README.md)
  * [Using SQL requests and procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/README.md)
* Writing entry points
  * [Procedures](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Procedure/README.md)
  * [SQL requests](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Application/Sql/README.md)
* [Documenting entry points](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/README.md)
* [The database link](https://github.com/dbeurive/backend/blob/master/src/Database/Link/README.md)
* [The database interface](https://github.com/dbeurive/backend/blob/master/src/Database/README.md)
* [Extension for PHP Unit](https://github.com/dbeurive/backend/tree/master/src/Phpunit)
* [Generating the database's access layer documentation](https://github.com/dbeurive/backend/blob/master/src/Cli/Bin/README.md) - that is: the SQLite database.

# Additional notes

* [Using the Makefile](https://github.com/dbeurive/backend/blob/master/doc/MAKEFILE.md)







 
 




 