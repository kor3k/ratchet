<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Test\Chat;

require dirname(__DIR__) . '/vendor/autoload.php';

//$server = IoServer::factory(
//    new Chat(),
//    8080
//);

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();

//http://socketo.me/docs/hello-world