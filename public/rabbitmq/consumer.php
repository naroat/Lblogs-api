<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);


$config = [
    'host' => '127.0.0.1',
    'vhost' => '/',
    'port' => '15672',
    'login' => 'guest',
    'password' => 'guest'
];


//连接broker, 创建一个rabbitmq连接
$cn = new AMQPConnection($config);


if ($cn->connect()) {
    exit('连接失败!');
}

$channel = new AMQPChannel();

//声明队列
$channel->queue_declare('hello', false, false, false);

//将消息发布到队列
$msg = new AMQPMessage('Hello World！');
$channel-> basic_publish($msg, '', 'hello');

//关闭通道和连接
$cn->close();
$channel->close();

