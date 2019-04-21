<?php

$client = new \Swoole\Client(SWOOLE_SOCK_TCP);

if (!$client->connect('127.0.0.1', 9501, 0.5)) {
    die('connect failed.');
}

if (!$client->send("hello world!")) {
    die("send failed.\n");
}

$data = $client->recv();
if (!$data) {
    die("recv failed.\n");
}

var_dump($data);
$client->close();
