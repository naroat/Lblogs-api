<?php

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

$server = new Swoole\Server('127.0.0.1', 9500);

//监听连接
$server->on('connect', function ($server, $fd) {
    echo 'connect ok!';
});

//接收
$server->on('receive', function ($server, $fd, $from_id, $data) {
    echo $fd;
    $server->send($fd, $data);
});

//关闭
$server->on('close', function ($server, $fd) {
    echo 'close ok!';
});

$server->start();

