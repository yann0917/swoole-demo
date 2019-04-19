<?php

$serv = new \Swoole\Server('127.0.0.1', 9501);

$config = [
    'task_worker_num' => 10,
];

$serv->set($config);

$serv->on('connect', function ($serv, $fd) {
    echo "Client:Connect.\n";
});

$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $task_id = $serv->task($data);
    echo "Dispath AsyncTask: id=$task_id\n";
});

$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    echo "New AsyncTask[id=$task_id]\n";
    $serv->finish("$data ---> OK");
});

$serv->on('finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data";
});

$serv->start();
