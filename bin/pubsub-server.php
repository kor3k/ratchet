<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use Test\PubSub;
use Test\MessageLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require dirname(__DIR__) . '/vendor/autoload.php';

$logger = new Logger('wsserver');
$logger->pushHandler( new StreamHandler( dirname(__DIR__).'/logs/server.log' ) );
$logger->pushHandler( new StreamHandler( 'php://stdout' ) );


$server = IoServer::factory(
    new HttpServer(
        new WsServer(
                new WampServer(
                    new MessageLogger(
                        new PubSub( $logger )
                        , $logger
                    )
                )
        )
    )
    , 8080
);

$server->run();

//IoServer > IpBlackList > HttpServer > OriginCheck > Router > WsServer > MessageLogger > Session > WampServer > Pusher
//IoServer > IpBlackList > FlashPolicy
//http://socketo.me/docs/hello-world
//https://github.com/Devristo/phpws