## 创建 TCP 服务

swoole_server是异步服务器，所以是通过监听事件的方式来编写程序的。
当对应的事件发生时底层会主动回调指定的PHP函数。
如当有新的TCP连接进入时会执行onConnect事件回调，当某个连接向服务器发送数据时会回调onReceive函数。

运行 `php tcp_start.php` 启动简单的 TCP 服务器
<details> <summary> example </summary>

[code](/src/quick-start/tcp_server.php)
</details>

**查看进程**

默认使用SWOOLE_PROCESS模式，因此会额外创建 `Master` 和 `Manager` 两个进程。
在设置 `worker_num` 之后，实际会出现 `2 + worker_num`个进程。

```shell
  ~  ps -ef|grep tcp_server |grep -v grep
root     28228 28061  0 16:23 pts/0    00:00:00 php tcp_server.php
root     28229 28228  0 16:23 pts/0    00:00:00 php tcp_server.php
root     28232 28229  0 16:23 pts/0    00:00:00 php tcp_server.php
root     28233 28229  0 16:23 pts/0    00:00:00 php tcp_server.php
```

`pstree -a` 以树状图显示进程，相同名称的进程不合并显示，并且会显示命令行参数，-p参数表示显示每个进程的PID

```bash
  ~  pstree -ap |grep tcp_server
  |       |   `-php,28228 tcp_server.php
  |       |       |-php,28229 tcp_server.php
  |       |       |   |-php,28232 tcp_server.php
  |       |       |   `-php,28233 tcp_server.php
```

* 服务器可以同时被成千上万个客户端连接，`$fd` 就是客户端连接的唯一标识符
* 调用 `$server->send()` 方法向客户端连接发送数据，参数就是 `$fd` 客户端标识符
* 调用 `$server->close()` 方法可以强制关闭某个客户端连接
* 客户端可能会主动断开连接，此时会触发 `onClose` 事件回调

## 创建 UDP 服务器

启动 `Server` 后，客户端无需 `Connect` ，直接可以向 `Server` 监听的 `9502` 端口发送数据包。
对应的事件为onPacket。

<details> <summary> example </summary>

[code](/src/quick-start/udp_server.php)
</details>

可以使用`netcat -u 127.0.0.1 9502`  连接 UDP 服务器

<details><summary> netcat 安装方法</summary>

下载:`wget http://sourceforge.net/projects/netcat/files/netcat/0.7.1/netcat-0.7.1-1.i386.rpm` <br>
执行安装: `rpm -ihv netcat-0.7.1-1.i386.rpm`
</details>

连接 UDP 服务器的客户端信息如下：

```php
array(4) {
  ["server_socket"]=>
  int(3)
  ["server_port"]=>
  int(9502)
  ["address"]=>
  string(9) "127.0.0.1"
  ["port"]=>
  int(41894)
}
```

## 创建 Web 服务器

`Http` 服务器只需要关注请求响应即可，所以只需要监听一个 `onRequest` 事件。当有新的Http请求进入就会触发此事件。

事件回调函数有2个参数，一个是 `request` 对象，包含了请求的相关信息，如 `GET/POST` 请求的数据。
另外一个是 `response` 对象，对 `request` 的响应可以通过操作 `response` 对象来完成。
`$response->end()` 方法表示输出一段HTML内容，并结束此请求。

程序可以根据 `$request->server['request_uri']` 实现路由。

<details> <summary> example </summary>

[code](/src/quick-start/http_server.php)
</details>

使用 `Chrome` 浏览器会产生一次额外的 `/favicon.ico` 请求，如下所示

```php
array(10) {
  ["request_method"]=>
  string(3) "GET"
  ["request_uri"]=>
  string(1) "/"
  ["path_info"]=>
  string(1) "/"
  ["request_time"]=>
  int(1555494004)
  ["request_time_float"]=>
  float(1555494005.2544)
  ["server_port"]=>
  int(9501)
  ["remote_port"]=>
  int(29492)
  ["remote_addr"]=>
  string(15) "115.206.115.247"
  ["master_time"]=>
  int(1555494004)
  ["server_protocol"]=>
  string(8) "HTTP/1.1"
}

array(10) {
  ["request_method"]=>
  string(3) "GET"
  ["request_uri"]=>
  string(12) "/favicon.ico"
  ["path_info"]=>
  string(12) "/favicon.ico"
  ["request_time"]=>
  int(1555494005)
  ["request_time_float"]=>
  float(1555494005.9308)
  ["server_port"]=>
  int(9501)
  ["remote_port"]=>
  int(29492)
  ["remote_addr"]=>
  string(15) "115.206.115.247"
  ["master_time"]=>
  int(1555494005)
  ["server_protocol"]=>
  string(8) "HTTP/1.1"
}
```

## 创建 WebSocket 服务器

`WebSocket` 服务器是建立在 `Http` 服务器之上的长连接服务器，客户端首先会发送一个 `Http` 的请求与服务器进行握手。握手成功后会触发 `onOpen` 事件，表示连接已就绪，`onOpen` 函数中可以得到 `$request` 对象，包含了Http握手的相关信息，如GET参数、Cookie、Http头信息等。

建立连接后客户端与服务器端就可以双向通信了。

<details> <summary> example </summary>

[code](/src/quick-start/ws_server.php)
</details>

* 客户端向服务器端发送信息时，服务器端触发 `onMessage` 事件回调
* 服务器端可以调用 `$server->push()` 向某个客户端（使用 `$fd` 标识符）发送消息
* 服务器端可以设置 `onHandShake` 事件回调来手工处理 `WebSocket` 握手
* `swoole_http_server` 是 `swoole_server` 的子类，内置了 `Http` 的支持
* `swoole_websocket_server` 是 `swoole_http_server` 的子类， 内置了 `WebSocket`的支持

## 设置定时器

定时器粒度为毫秒级
<details> <summary> example </summary>

[code](/src/quick-start/timer.php)
</details>

* `swoole_timer_tick` 函数是持续触发的
* `swoole_timer_after` 函数仅在约定的时间触发一次
* `swoole_timer_tick` 和 `swoole_timer_after` 函数会返回一个整数，表示定时器的ID
* 可以使用 `swoole_timer_clear` 清除此定时器，参数为定时器ID

## 执行异步任务

如果需要执行一个很耗时的任务，可以投递一个异步任务到 `TaskWork` 进程池中执行，不影响当前请求的处理速度。

<details> <summary> example </summary>

[code](/src/quick-start/tcp_task_server.php)
</details>

步骤：

1. 服务器设置 `task_worker_num` , 可以根据任务的耗时和任务量配置适量的task进程
2. `onReceive` 回调中使用 `$serv->task()` 投递异步任务
3. `onTask` 事件回调函数中处理异步任务
4. `onFinish` 事件回调函数中处理异步任务的结果（可选）

在设置 `task_worker_num` 之后，实际会出现 `2 + worker_num + task_worker_num`个进程

<details> <summary> ps 查看进程 </summary>

```bash
  ~  ps -ef| grep tcp_task_server |grep -v grep
root     32051 31660  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32052 32051  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32055 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32056 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32057 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32058 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32059 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32060 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32061 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32062 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32063 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32064 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32065 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
root     32066 32052  0 11:16 pts/0    00:00:00 php tcp_task_server.php
```

```bash
  ~  pstree -ap |grep tcp_task_server
  |       |   `-php,32051 tcp_task_server.php
  |       |       |-php,32052 tcp_task_server.php
  |       |       |   |-php,32055 tcp_task_server.php
  |       |       |   |-php,32056 tcp_task_server.php
  |       |       |   |-php,32057 tcp_task_server.php
  |       |       |   |-php,32058 tcp_task_server.php
  |       |       |   |-php,32059 tcp_task_server.php
  |       |       |   |-php,32060 tcp_task_server.php
  |       |       |   |-php,32061 tcp_task_server.php
  |       |       |   |-php,32062 tcp_task_server.php
  |       |       |   |-php,32063 tcp_task_server.php
  |       |       |   |-php,32064 tcp_task_server.php
  |       |       |   |-php,32065 tcp_task_server.php
  |       |       |   `-php,32066 tcp_task_server.php

```

</details>

## 创建同步 TCP Client

## 创建异步 TCP Client

## 使用异步客户端

## 多进程共享数据

## 使用协程客户端
