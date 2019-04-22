<?php

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use Dotenv\Dotenv;

Swoole\Runtime::enableCoroutine();

$dotenv = Dotenv::create(dirname(__DIR__, 2));
$dotenv->load();

$db = new Swoole\Coroutine\MySQL();

$server = [
    'host' => getenv('MYSQL_HOST'),
    'port' => getenv('MYSQL_PORT'),
    'user' => getenv('MYSQL_USERNAME'),
    'password' => getenv('MYSQL_PASSWORD'),
    'database' => getenv('MYSQL_DATABASE'),
    'charset' => 'utf8',
    'timeout' => 2,
];

$db->connect($server);

$result = $db->query('show tables');
var_dump($result);
