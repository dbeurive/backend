# Description

This namespace defines SQL helpers.

These helpers are intended to be used within the classes that implement SQL requests.
   
Because, *by conventions*, the names associated to fields’ values *should* be fully qualified (relatively to the tables), it is recommended to write requests using this format:

Assuming that the database's schema is:

    CREATE TABLE IF NOT EXISTS `user` (
        `id`          INT UNSIGNED       NOT NULL AUTO_INCREMENT,
        `login`       VARCHAR(255)       NOT NULL,
        `password`    VARCHAR(255)       NOT NULL,
        `description` VARCHAR(255)       NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE INDEX `login_idx` (`login`)
    ) ENGINE = InnoDB
      CHARACTER SET utf8;

Then, a SELECT request *should* be written:

    SELECT id          AS 'user.id',
           login       AS 'user.login',
           password    AS 'user.password',
           description AS 'user.description'
    FROM   user

Or:

    SELECT `user`.`id`          AS 'user.id',
           `user`.`login`       AS 'user.login',
           `user`.`password`    AS 'user.password',
           `user`.`description` AS 'user.description'
    FROM   `user`

The important point is that the returned values are associated to the names `'user.id'`, `'user.login'`, `'user.password'` and `'user.description'`.
These names contain the name of the table.
 
Of course, this is not an obligation. In practice, you can rename the fields as you see fit.
However, when you document your SQL requests and you procedures, the [API](https://github.com/dbeurive/backend/blob/master/src/Database/Entrypoints/Description/README.md) will check that the fields' names you mention really exist within the database.

For example, let's consider the following example:

```php
    class Select extends AbstractApplication {
    
        public function _init(array $inInitConfig=[]) { /* ... */ }
    
        protected function _validateExecutionConfig(array $inExecutionConfig, &$outErrorMessage) { /* ... */ }
    
        protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector) { /* ... */ }
    
        /**
         * @see \dbeurive\Backend\Database\Entrypoints\AbstractEntryPoint
         */
        public function getDescription() {
            $doc = new \dbeurive\Backend\Database\Entrypoints\Description\Sql();
            $doc->setDescription('This request selects a batch of users')
                ->setType($doc::TYPE_SELECT)
                ->setSql(SELECT id AS 'user.id', login AS 'user.login', password AS 'user.password', description AS 'user.description' FROM user)
                ->addTable('user')
                ->setSelectedFields(['user.id', 'user.login', 'user.password', 'user.description']);
    
            return $doc;
        }
    }
```
    
The line below says that the SQL request returns `'user.id'`, `'user.login'`, `'user.password'` and `'user.description'`.

```php
    setSelectedFields(['user.id', 'user.login', 'user.password', 'user.description']);
```

> Please note that you can also write: `setSelectedFields( ['user.*'] )`.


*Backend* will verify that **these fields really exist** within the database.
To do that, *Backend* looks at the database's schema, which defines fields’ names as "`<table name>.<field name>`".

Please note that you can write your SQL request like this:

```sql
SELECT id, login, password, description FROM user
```

However, if you do that, then you have to transform the result in order to conform to the naming conventions.

For example:

```php
    protected function _execute(array $inExecutionConfig, AbstractConnector $inConnector) {
        /* @var \PDO $pdo */
        $pdo = $inConnector->getDatabaseHandler();

        // Execute the request.
        $result = new BaseResult();
        
        $req = $pdo->prepare('SELECT id, login, password, description FROM user');
        if (false === $req->execute($fieldsValues)) {
            $message = "SQL request failed\n";
            $result->setErrorMessage($message);
            return $result;
        }

        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // HERE: transform the result so the it conforms to the naming conventions.
        //       Names must be "user.id", not "id".
        //                     "user.login", not "login".
        //                     ... and so on.
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        $data = transform($data);
        
        $result->setDataSets($data);
        return $result;
    }
```

## getFullyQualifiedFieldsAsSql($inTableName, $inFields)

```php
    getFullyQualifiedFieldsAsSql('user', ['id', 'login']);           // => "user.id AS 'user'.'id', user.login AS 'user.login'"
     
    getFullyQualifiedFieldsAsSql('user', ['user.id', 'login']);      // => "user.id AS 'user'.'id', user.login AS 'user.login'"
    
    getFullyQualifiedFieldsAsSql('user', ['user.id', 'user.login']); // => "user.id AS 'user'.'id', user.login AS 'user.login'"
```

## getFullyQualifiedQuotedFieldsAsSql($inTableName, $inFields)
 
```php
    getFullyQualifiedQuotedFieldsAsSql('user', ['id', 'login']);           // => "`user`.`id` AS 'user'.'id', `user`.`login` AS 'user.login'"
    
    getFullyQualifiedQuotedFieldsAsSql('user', ['user.id', 'login']);      // => "`user`.`id` AS 'user'.'id', `user`.`login` AS 'user.login'"
    
    getFullyQualifiedQuotedFieldsAsSql('user', ['user.id', 'user.login']); // => "`user`.`id` AS 'user'.'id', `user`.`login` AS 'user.login'"
```

