<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$logger = new \Monolog\Logger( 'wspusher' );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( dirname(__DIR__).'/logs/client.log' ) );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( 'php://output' ) );


$loop       =   React\EventLoop\Factory::create();
$pusher     =   new \Test\Pusher( $loop, $logger );
$wsClient   =   new WebSocketClient( $pusher , $loop );

$pusher
    ->boot()
    ->push( 'public' , 'i am pushing' )
;

$logger->info( '<< between the pushes >>' );

$pusher
    ->push( 'public' , 'pushing again' )
    ->push( 'public' , 'more push' )
    ->disconnect()
;


