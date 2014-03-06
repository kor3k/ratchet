<?php

namespace Test;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

abstract class AbstractClient implements \WebSocketClient\WebSocketClientInterface
{
    /**
     * @var \WebSocketClient
     */
    protected   $client;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected   $logger;

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
        $this->logger   =   $logger;
        $this->loop     =   $loop;
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

    public function call($proc, $args, Closure $callback = null)
    {
        $this->client->call($proc, $args, $callback);
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
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }
}