<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

/**
 * websocket
 */
$ws = new Swoole\Websocket\Server('127.0.0.1', 9502);

//监听打开事件
$ws->on('open', function ($server, $request) {
    $server->push($request->fd, 'open ws!');
});

//监听消息事件
$ws->on('message', function ($server, $frame) {
    //广播消息
    foreach ($server->connection_list() as $fd) {
        $server->push($fd, $fd . '广播消息！');
    }

    $server->push($frame->fd, $frame->fd . '单条消息');

    //可靠消息投递

});

$ws->on('close', function ($server, $fd) {
    echo 'close';
});

$ws->start();