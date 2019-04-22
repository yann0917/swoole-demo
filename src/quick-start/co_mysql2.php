<?php

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use Dotenv\Dotenv;

// Swoole\Runtime::enableCoroutine();

$dotenv = Dotenv::create(dirname(__DIR__, 2));
$dotenv->load();

$http = new Swoole\Http\Server('0.0.0.0', 9501);

$http->on('request', function ($request, $response) {
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
    $result = $db->query('select * from user');
    $response->end(json_encode($result, JSON_UNESCAPED_UNICODE));
});

$http->start();
