<?php

$serv = new \Swoole\Server('127.0.0.1', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$serv->on('Packet', function ($serv, $data, $client) {
    $serv->sendto($client['address'], $client['port'], 'Server '. $data);
    var_dump($client);
});

$serv->start();
