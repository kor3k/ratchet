<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$logger     =   new \Monolog\Logger( 'wspusher' );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( dirname(__DIR__).'/logs/client.log' ) );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( 'php://output' ) );

$pusher     =   \Test\Pusher::create( $logger );

$pusher
    ->boot()
    ->push( 'public' , 'i am pushing' )
;

$logger->info( '<< 5s pause between the pushes >>' );
sleep(5);

$pusher
    ->push( 'public' , 'pushing again' )
    ->push( 'public' , 'more push' )
    ->disconnect()
;


