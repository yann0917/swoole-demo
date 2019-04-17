<?php

$http = new \Swoole\Http\Server('0.0.0.0', 9501);

$http->on('request', function ($request, $response) {
    var_dump($request->get, $request->post);
    var_dump($request->server);

    $response->header("Content-type", "text/html;charset=utf-8");
    $response->end("<h1>Hello Swoole " . rand(100, 999) . "</h1>");
});

$http->start();
