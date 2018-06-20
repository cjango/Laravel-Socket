<?php

namespace RuLong\Socket;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class Commands extends Command
{
    protected $signature = 'workerman {action} {--d}';

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
        $worker->count           = config('workerman.cpu') * 2;
        $worker->registerAddress = config('workerman.register');
        $worker->eventHandler    = config('workerman.handler');
    }

    private function startGateWay()
    {
        if (config('workerman.wss') == true) {
            $gateway            = new Gateway("websocket://" . config('workerman.port'), [config('workerman.ssl')]);
            $gateway->transport = 'ssl';
        } else {
            $gateway = new Gateway("websocket://" . config('workerman.port'));
        }

        $gateway->name                 = 'Gateway';
        $gateway->count                = config('workerman.cpu');
        $gateway->lanIp                = '127.0.0.1';
        $gateway->startPort            = 2300;
        $gateway->pingInterval         = 30;
        $gateway->pingNotResponseLimit = 0;
        $gateway->pingData             = config('workerman.heart');
        $gateway->registerAddress      = config('workerman.register');
    }

    private function startRegister()
    {
        new Register('text://' . config('workerman.register'));
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
