<?php

namespace Test;

use WebSocketClient\WebSocketClientInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use Psr\Log\LoggerInterface,
    Psr\Log\LoggerAwareInterface,
    Psr\Log\LoggerAwareTrait;

abstract class AbstractClient implements WebSocketClientInterface , LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var \WebSocketClient
     */
    protected   $client;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected   $loop;

    /**
     * @param \React\EventLoop\LoopInterface    $loop
     * @param \Psr\Log\LoggerInterface          $logger
     */
    public function __construct( LoopInterface $loop , LoggerInterface $logger )
    {
        $this->loop     =   $loop;
        $this->setLogger( $logger );
    }

    public function onWelcome(array $data)
    {
        $this->logger->info( 'connected' , $data );
    }

    public function onEvent($topic, $message)
    {
        $this->logger->info( 'event' , [ 'topic' => $topic , 'message' => $message ] );
    }

    public function subscribe($topic)
    {
        $this->client->subscribe($topic);
        $this->logger->info( 'subscribe' , [ 'topic' => $topic ] );

        return $this;
    }

    public function unsubscribe($topic)
    {
        $this->client->unsubscribe($topic);
        $this->logger->info( 'unsubscribe' , [ 'topic' => $topic ] );

        return $this;
    }

    public function call($proc, $args, \Closure $callback = null)
    {
        $this->client->call( $proc , [ $args ] , $callback );
        $this->logger->info( 'call' , [ 'proc' => $proc , 'args' => $args ] );

        return $this;
    }

    public function publish($topic, $message)
    {
        $this->client->publish($topic, $message);
        $this->logger->info( 'publish' , [ 'topic' => $topic , 'message' => $message ] );

        return $this;
    }

    public function disconnect()
    {
        $this->logger->info( 'disconnected' );
        $this->client->disconnect();

        return $this;
    }

    /**
     * @param \WebSocketClient $client
     */
    public function setClient(\WebSocketClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return \WebSocketClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * @param LoggerInterface $logger
     * @return static
     */
    public static function create( LoggerInterface $logger )
    {
        $loop       =   LoopFactory::create();
        $client     =   new static( $loop, $logger );
        new \WebSocketClient( $client , $loop );

        return $client;
    }
}