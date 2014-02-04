<?php

use Ratchet\Server\IoServer;
use Test\Chat;

require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new Chat(),
    8080
);

$server->run();

//http://socketo.me/docs/hello-world