# 启动 TCP 服务

运行 `php tcp_start.php` 启动简单的 TCP 服务器

查看进程

```shell
 ~  ps -ef|grep tcp_server |grep -v grep
root     26517  1157  0 22:10 pts/1    00:00:00 php tcp_server.php
root     26518 26517  0 22:10 pts/1    00:00:00 php tcp_server.php
root     26521 26518  0 22:10 pts/1    00:00:00 php tcp_server.php
root     26522 26518  0 22:10 pts/1    00:00:00 php tcp_server.php
```

```shell
 ~  pstree -a |grep tcp_server
  |       |   `-php tcp_server.php
  |       |       |-php tcp_server.php
  |       |       |   |-php tcp_server.php
  |       |       |   `-php tcp_server.php
```

* 服务器可以同时被成千上万个客户端连接，`$fd` 就是客户端连接的唯一标识符
* 调用 `$server->send()` 方法向客户端连接发送数据，参数就是 `$fd` 客户端标识符
* 调用 `$server->close()` 方法可以强制关闭某个客户端连接
* 客户端可能会主动断开连接，此时会触发 `onClose` 事件回调