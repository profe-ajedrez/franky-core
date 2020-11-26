# Franky Core

A simple class to start fast api and app development with PHP


## Install

Just clone the repo somewhere in you local dev machine and run composer install/update

```bash
$ git clone https://profe-ajedrez-andres@bitbucket.org/paredestar/franki_core.git
$ cd put_here_franky_folder
$ composer install
$ composer update
```

In your browser go to http://your dev url/franky_folder/tests/core/index.php



## Api

The functionality of franky is exposed in the object returned by the facory 'FrankyCore::getApp()'

To create the franky object you have to pass as parameters an array with the db config an
 another array with the folder config, like in the example.

```php
$franky = FrankyCore::getApp(
    [
        'database' => 'your db name',
        'username' => 'your db user',
        'password' => 'your db pass',
        'host'     => 'your db host',
        'type'     => 'your db type (mysql, postgre, etc)'
    ],
    [
        'rootPath'  => 'path to the root folder of your app',
        'viewPath'  => 'path to the root folder of your views',
        'assetPath' => 'path to the root folder of your assets',
        'cssPath'   => 'path to the root folder of your css',
        'logPath'   => 'path to the root folder of your log file',
    ]
);
```

The franky object will expose this api:

### mailer

Is the adapter used to send mails from the app/api (actually is an adapter wrapper fro PHPMailer )

```php
$mailer = $franky->mailer();
```


### db 

Id the adapter which wraps the DB connection

```php
$db = $franky->db();
```


#### The query builder


##### Select example

```php
$db = $franky->db();
$sql = $db->createSql();

$sql->select()->from('users');
echo $sql;  // echoes SELECT * FROM `users`
```


##### Select example with join

```php
$db = $franky->db();
$sql = $db->createSql();

$sql->select(['id', 'username', 'email'])->from('users')
    ->leftJoin('user_info', ['users.id' => 'user_info.user_id'])
    ->where('id < :id')
    ->orderBy('id', 'DESC');

echo $sql;
/*
Echoes:

SELECT `id`, `username`, `email` FROM `users`
LEFT JOIN `user_info` ON (`users`.`id` = `user_info`.`user_id`)
WHERE (`id` < ?) ORDER BY `id` DESC
*/
```



##### Insert example with prepared parameters

```php
$db = $franky->db();
$sql = $db->createSql();

$sql->insert('users')->values(
    [
        'username' => ':username',
        'password' => ':password'
    ]
);
echo $sql;  //echoes INSERT INTO `users` (`username`, `password`) VALUES (?, ?)
```

##### Delete example

```php
$db = $franky->db();
$sql = $db->createSql();

$sql->delete('users')->where('id = :id');
echo $sql;  // DELETE FROM `users` WHERE (`id` = ?)
```

##### Update example

```php
$db = $franky->db();
$sql = $db->createSql();

$sql->update('users')->values([
    'username' => ':username',
    'password' => ':password'
])->where('id = :id');
echo $sql; // echoes UPDATE `users` SET `username` = ?, `password` = ? WHERE (`id` = ?)
```

##### Executing the builded query

Simple qury without prepared statement
```php
$db = $franky->db();
$db->query((string)$sql);

while (($row = $db->fetch())) {
    print_r($row);
}
```


Query with prepared statement
```php
$db = $franky->db();
$db->prepare((string)$sql)
   ->bindParams(['id' => 1000])
   ->execute();

$rows = $db->fetchAll();

foreach ($rows as $row) {
    print_r($row);
}
```


### environment

With this, we can know if we are in dev, prod or qa environment (or any custom environment)

```php
$env = $franky->environment(); 
```


### config

Returns the configuration array

```php
$config = $franky->config();
```

### viewPath

Returns the path to the views folder as a string

```php
$viewPath = $franky->viewPath();
```


### assetPath

Returns the path to the assets folder as a string

```php
$assetPath = $franky->assetPath();
```


### cssPath

Returns the path to the css folder as a string

```php
$cssPath = $franky->cssPath();
```


### session

Returns the session helper object (a simple wrapper to work with `$_SESSION`)

```php
$session = $franky->session();

// Con la instancia del helper de sessiones podemos establecer valores de sesión...
$session->foo   = 'bar';
$session['baz'] = 123;

// ...Y acceder a esos vlaores
echo $session['foo'];
echo $session->baz;

// Eliminar valores de sesión
unset($session->foo);
unset($session['baz']);
```

#### Uso avanzado del objeto helper de sesiones

Valores de sesión con tiempo de expiración
```php
$session = $franky->session();

// Establece un valor de sesión con duración. En este ejemplo, $session->caca tendrá el vlaor 'pipi' durante 100 segundos.
$session->setTimedValue('caca', 'pipi', 100);

if (isset($session->foo) && $session->foo === 'pipi') {
    echo $session->foo;
} else {
    echo 'No!';
}
```


Valores de sesión disponibles un número fijo de intentos
```php
$session = $franky->session();

// Establece un valor de sesión que estará disponible durante 3 intentos de petición
$session->setRequestValue('tula', 'orina', 3); 

echo $session->tula;  // echoes 'orina'
echo $session->tula;  // echoes 'orina'
echo $session->tula;  // echoes 'orina'

echo $session->tula;  // $session->tula doesnt exists.
```


### log

Returns the logger object of the app

```php
$log = $franky->log();
$log->error("we are logging an error");
$log->warning("we are logging a warning");
$log->info("we are logging a debug info message");
```