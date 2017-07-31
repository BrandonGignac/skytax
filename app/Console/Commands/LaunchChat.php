<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Vanguard\Features\PrivateChat;

class LaunchChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'chat:serve';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $description = 'Start chat server.';

    /**
     * Creates a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting chat web socket server on port 8010");

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new PrivateChat()
                )
            ),
            8010,
            '0.0.0.0'
        );

        $server->run();
    }
}
