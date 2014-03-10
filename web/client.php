<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$logger     =   new \Monolog\Logger( 'wsclient' );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( dirname(__DIR__).'/logs/client.log' ) );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( 'php://output' ) );

$client     =   \Test\Client::create( $logger );

$client->getLoop()->run();

$client->disconnect();
