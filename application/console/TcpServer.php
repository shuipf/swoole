<?php

namespace app\Console;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class TcpServer extends Command
{
    protected $server;

    // 命令行配置函数
    protected function configure()
    {
        // setName 设置命令行名称
        // setDescription 设置命令行描述
        $this->setName('tcp:start')->setDescription('Start TCP Server!');
    }

    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {
        $this->server = new \swoole_server('0.0.0.0', 9501);

        $this->server->set(
            [
                'worker_num' => 4,
                'daemonize' => false,
            ]
        );

        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Close', [$this, 'onClose']);

        $this->server->start();
        // $output->writeln("TCP: Start.\n");
    }

    // 主进程启动时回调函数
    public function onStart(\swoole_server $serv)
    {
        echo "Start\n";
    }

    // 建立连接时回调函数
    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
        echo "Connect\n";
    }

    // 收到信息时回调函数
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        echo "message: {$data} form Client: {$fd} \n";
        // 将受到的客户端消息再返回给客户端
        $server->send($fd, "Message form Server: " . $data);
    }

    // 关闭连时回调函数
    public function onClose(\swoole_server $server, $fd, $from_id)
    {
        echo "Close\n";
    }
}