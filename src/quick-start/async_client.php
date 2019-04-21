<?php

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on('connect', function ($client) {
    $client->send("hello world\n");
});

$client->on('receive', function ($client, $data) {
    echo "Received:". $data .PHP_EOL;
});

$client->on('error', function ($client) {
    echo "Connect failed.\n";
});

$client->on('close', function ($client) {
    echo "Connection close\n";
});

$client->connect('127.0.0.1', 9501, 0.5);
