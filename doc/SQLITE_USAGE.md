# What you can do with the generated documentation

The file [mysql_doc.sqlite](https://github.com/dbeurive/backend/blob/master/tests/cache/mysql_doc.sqlite) contains the generated documentation.

Open the SQLite database:

```sqlite
$ sqlite3 mysql_doc.sqlite
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


