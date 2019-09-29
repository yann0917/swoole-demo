<?php

// 获取 swoole 版本号
$version = swoole_version();
var_dump($version);

// 获取本机网卡Mac地址
$local_ips = swoole_get_local_ip();
var_dump($local_ips);

// 获取本机CPU核数
$cpu_num = swoole_cpu_num();
var_dump($cpu_num);
