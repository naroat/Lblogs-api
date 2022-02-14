<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

/**
 * 异步风格
 */
$client = new Swoole\Async\Client(SWOOLE_SOCK_TCP);

$client->on('connect', function ($client) {
    $client->send('my taoran');
});

//接收
$client->on('receive', function ($client, $data) {
    //将会在输出内容后，再输出当前内容
    echo 'receive' . $data;
});

//错误
$client->on('error', function ($client) {

});

//关闭
$client->on('close', function ($client) {

});

$client->connect('127.0.0.1', 9500, 500) || exit('失败：' .$client->errCode);

//输出
echo 'client ok!';