<?php

namespace quickStart;

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use Dotenv\Dotenv;

$dotenv = Dotenv::create(dirname(__DIR__, 2));
$dotenv->load();

$db = new Swoole\Mysql;

$server = [
    'host' => getenv('MYSQL_HOST'),
    'port' => getenv('MYSQL_PORT'),
    'user' => getenv('MYSQL_USERNAME'),
    'password' => getenv('MYSQL_PASSWORD'),
    'database' => getenv('MYSQL_DATABASE'),
    'charset' => 'utf8',
    'timeout' => 2,
];

$db->connect($server, function ($db, $result) {
    $db->query('show tables', function (Swoole\Mysql $db, $result) {
        var_dump($result);
        $db->close();
    });
});
