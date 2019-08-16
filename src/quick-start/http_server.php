<?php

$http = new \Swoole\Http\Server('0.0.0.0', 9501);

$http->on('request', function ($request, $response) {
    // var_dump($request->get, $request->post);
    // var_dump($request->server);
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        return $response->end();
    }

    $opts = [
        'http' => ['method' => 'GET','timeout'=>1],
    ];
    $context = stream_context_create($opts);
    file_get_contents('http://www.test.com', false, $context);
    $response->header("Content-type", "text/html;charset=utf-8");
    $response->end("<h1>Hello Swoole " . rand(100, 999) . "</h1>");
});

$http->start();
