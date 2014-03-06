<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$logger = new \Monolog\Logger( 'wsclient' );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( dirname(__DIR__).'/logs/client.log' ) );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( 'php://output' ) );


$loop       =   React\EventLoop\Factory::create();
$client     =   new \Test\Client( $loop , $logger );
$wsClient   =   new WebSocketClient( $client , $loop );

$loop->run();

$client->disconnect();
