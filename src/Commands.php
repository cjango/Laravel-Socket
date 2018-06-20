<?php

namespace RuLong\Socket;

use App\Workerman\Events;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class Commands extends Command
{
    protected $signature = 'workman {action} {--d}';

    protected $description = 'Operate a Workerman server.';

    public function handle()
    {
        $this->info('Welcome use Workerman server');

        global $argv;
        $action = $this->argument('action');
        if (!in_array($action, ['start', 'stop', 'restart', 'reload', 'status', 'connections'])) {
            $this->error('Action not allow');
            return;
        }
        $this->checkEventFile();
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        $this->server();
    }

    protected function server()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
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

    private function checkEventFile()
    {
        if (!is_dir(app_path('Workerman'))) {
            $this->warn('Workerman Path Notexist');
            $this->laravel['files']->makeDirectory(app_path('Workerman'), 0755, true, true);
            $this->info('Make Workerman Path was created.');
        }

        $eventFile = app_path('Workerman/Events.php');
        if (file_exists($eventFile)) {
            $this->info('Workerman EventFile already exists');
        } else {
            $contents = $this->getStub('event');
            $this->warn('Workerman EventFile Notexist');
            $this->laravel['files']->put($eventFile, $contents);
            $this->info('Make Workerman EventFile was created.');
        }
    }

    private function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__ . "/stubs/$name.stub");
    }

}
