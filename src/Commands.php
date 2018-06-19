<?php

namespace RuLong\Socket;

use Illuminate\Console\Command;

class Commands extends Command
{
    protected $signature = 'workman {action} {--d}';

    protected $description = 'Operate a Workerman server.';

    public function handle()
    {
        global $argv;
        $action = $this->argument('action');

        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        switch ($arg) {
            case 'start':
                $this->start();
                break;
            case 'stop':
                $this->stop();
                break;
            case 'restart':
                break;
            case 'reload':
                break;
            case 'status':
                break;
            case 'connections':
                break;
        }
    }

    protected function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    protected function stop()
    {

    }

    private function startBusinessWorker()
    {
        $worker                  = new BusinessWorker();
        $worker->name            = 'BusinessWorker';
        $worker->count           = 1;
        $worker->registerAddress = '127.0.0.1:1236';
        $worker->eventHandler    = Events::class;
    }

    private function startGateWay()
    {
        $context = [
            'ssl' => [
                'local_cert'  => '/yourSSLcert.pem',
                'local_pk'    => '/yourSSLcert.key',
                'verify_peer' => false,
            ],
        ];

        // 开启wss模式
        // $gateway = new Gateway("websocket://0.0.0.0:2346", $context);
        // $gateway->transport            = 'ssl';

        $gateway = new Gateway("websocket://0.0.0.0:2346");

        $gateway->name                 = 'Gateway';
        $gateway->count                = 1;
        $gateway->lanIp                = '127.0.0.1';
        $gateway->startPort            = 2300;
        $gateway->pingInterval         = 30;
        $gateway->pingNotResponseLimit = 0;
        $gateway->pingData             = '{"type":"@heart@"}';
        $gateway->registerAddress      = '127.0.0.1:1236';
    }

    private function startRegister()
    {
        new Register('text://0.0.0.0:1236');
    }
}
