## 创建 TCP 服务

swoole_server是异步服务器，所以是通过监听事件的方式来编写程序的。
当对应的事件发生时底层会主动回调指定的PHP函数。
如当有新的TCP连接进入时会执行onConnect事件回调，当某个连接向服务器发送数据时会回调onReceive函数。

运行 `php tcp_start.php` 启动简单的 TCP 服务器

查看进程

```shell
  ~  ps -ef|grep tcp_server |grep -v grep
root     28228 28061  0 16:23 pts/0    00:00:00 php tcp_server.php
root     28229 28228  0 16:23 pts/0    00:00:00 php tcp_server.php
root     28232 28229  0 16:23 pts/0    00:00:00 php tcp_server.php
root     28233 28229  0 16:23 pts/0    00:00:00 php tcp_server.php
```

`pstree -a` 以树状图显示进程，相同名称的进程不合并显示，并且会显示命令行参数，-p参数表示显示每个进程的PID

```shell
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

可以使用`netcat -u 127.0.0.1 9502`  连接 UDP 服务器

<details>

下载:`wget http://sourceforge.net/projects/netcat/files/netcat/0.7.1/netcat-0.7.1-1.i386.rpm` <br>
执行安装: `rpm -ihv netcat-0.7.1-1.i386.rpm`

<summary> netcat 安装方法</summary>
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
