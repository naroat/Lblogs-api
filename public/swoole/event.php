<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

use Swoole\Coroutine as Co;
//创建一个协程
/*go(function () {
    //io场景（阻塞时间）
    Co::sleep(1);
    echo '1' . PHP_EOL;
});

echo '3' . PHP_EOL;

go(function () {
    Co::sleep(1);
    echo '2' . PHP_EOL;
});*/

//协程能够承受并发的原因（即调用数据库，又调用redis）

//go(function () {
//    //数据查询
//    Co::sleep(3);
//});
//
//go(function () {
//    //接口调用
//    Co::sleep(1);
//});
//
//go(function () {
//    //redis查询
//    Co::sleep(1);
//});

$num = 30000;

for ($i = 0; $i < $num; $i++) {
    go(function () use ($i) {
        $redis = new Co\Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->set('name' . $i, 'taoran' . $i);
    });
}