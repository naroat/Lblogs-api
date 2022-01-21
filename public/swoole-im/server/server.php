<?php
/**
 * 服务端
 */

use Swoole\Table;

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

class Server
{
    /** @var string IP */
    private $ip;

    /** @var int 端口 */
    private $port;

    /** @var swoole server */
    private $server;

    public function __construct($ip = "127.0.0.1", $port = 9601)
    {
        $this->ip = $ip;
        $this->port = $port;

    }

    /**
     * 创建内存共享table
     */
    public function createTable()
    {
        //table_size: 最大行数
        $table = new Swoole\Table(1024);
        $table->column('fd', Swoole\Table::TYPE_INT);
        $table->column('username', Swoole\Table::TYPE_STRING, 64);
        $table->create();
        $this->server->table = $table;
    }

    /**
     * 启动服务
     */
    public function run()
    {
        //创建server对象
        $this->server = new Swoole\Server($this->ip, $this->port);

        //设置异步任务的工作进程数量
        $this->server->set([
            'worker_num' => 2,
//            'task_worker_num' => 4
        ]);

        //创建内存共享table
        $this->table = $this->createTable();

        //监听连接事件
        $this->server->on("Connect", function ($server, $fd) {
            //用户添加到table
            $this->addUser("username:" . $fd, $fd);
            //广播消息
            $this->broadcast($fd . " - 客户端连接!");
            $this->server->send($fd, "1：输入`handle:1`更改用户名称" . PHP_EOL);
            $this->server->send($fd, "2：输入`handle:2`查看用户列表" . PHP_EOL);
            $this->server->send($fd, "3：输入`handle:3`选择私聊用户" . PHP_EOL);
        });

        //监听接收事件
        $this->server->on("Receive", function ($server, $fd, $reactor_id, $data) {
            $this->handleSelect($fd, $data);
        });

        //监听关闭事件
        $this->server->on("Close", function ($server, $fd) {
            //广播消息
            $this->broadcast($fd . " - 断开连接!");
        });

        //处理异步任务
        /*$this->server->on("Task", function ($server, $task, $worker_id, $data) {
            $server->finish($data . PHP_EOL);
        });*/

        //处理异步任务的结果
        /*$this->server->on("Finish", function ($server, $task_id, $data) {
            echo $data;
        });*/

        $this->server->start();
    }

    /**
     * 新增用户
     */
    public function addUser($username, $fd)
    {

        $this->server->table[$fd] = [
            'username' => $username,
            'fd' => $fd
        ];
    }

    /**
     * 广播
     */
    public function broadcast($data)
    {
        foreach ($this->server->table as $key => $val) {
            $this->server->send($val['fd'], $data . PHP_EOL);
        }

    }

    /**
     * 获取用户列表
     */
    public function getUserList($fd)
    {
        $userList = '';
        foreach ($this->server->table as $key => $val) {
            $userList .= $val['username'] . PHP_EOL;
        }
        echo "用户列表：" . PHP_EOL;
        $this->server->send($fd, $userList);
    }

    /**
     * 选择操作
     */
    public function handleSelect($fd, $data)
    {

        $data = trim($data);
        //var_dump($data);
        switch ($data) {
            case "handle:1":
                //改名
                break;
            case "handle:2":
                //查看用户列表
                $this->getUserList($fd);
                break;
            default:
                $this->broadcast($data);
                break;
        }
    }
}



$server = new Server();
$server->run();

