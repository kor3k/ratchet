<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$logger = new \Monolog\Logger( 'wsclient' );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( dirname(__DIR__).'/logs/client.log' ) );

$loop = React\EventLoop\Factory::create();
$client = new WebSocketClient( new \Test\Client( $logger ) , $loop );

$loop->run();

