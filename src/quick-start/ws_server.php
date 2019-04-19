<?php

$ws = new \Swoole\WebSocket\Server('0.0.0.0', 9502);

$ws->on('open', function ($ws, $request) {
    var_dump($request->id, $request->get, $request->server);
    $ws->push($request->id, 'hello webSocket');
});

$ws->on('message', function ($ws, $frame) {
    echo "message: {$frame->data}\n";
    $ws->push($frame->id, "server: {$frame->data}");
});

$ws->on('close', function ($ws, $fd) {
    echo "Client-{$fd} is closed.\n";
});

$ws->start();
