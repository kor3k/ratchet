<?php

namespace Test;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

class Client implements \WebSocketClient\WebSocketClientInterface
{
    /**
     * @var \WebSocketClient
     */
    private $client;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    public function __construct( LoggerInterface $logger , LoopInterface $loop )
    {
        $this->logger   =   $logger;
        $this->loop     =   $loop;
    }

    public function onWelcome(array $data)
    {
        $this->logger->info( 'connected' , $data );

        $this->subscribe( 'public' );
        $this->publish( 'public' , 'jupijou' );
    }

    public function onEvent($topic, $message)
    {
        $this->logger->info( 'event' , [ 'topic' => $topic , 'message' => $message ] );
        $this->loop->stop();
    }

    public function subscribe($topic)
    {
        $this->client->subscribe($topic);
        $this->logger->info( 'subscribe' , [ 'topic' => $topic ] );

    }

    public function unsubscribe($topic)
    {
        $this->client->unsubscribe($topic);
        $this->logger->info( 'unsubscribe' , [ 'topic' => $topic ] );

    }

    public function call($proc, $args, Closure $callback = null)
    {
        $this->client->call($proc, $args, $callback);
        $this->logger->info( 'call' , [ 'proc' => $proc , 'args' => $args ] );

    }

    public function publish($topic, $message)
    {
        $this->client->publish($topic, $message);
        $this->logger->info( 'publish' , [ 'topic' => $topic , 'message' => $message ] );
    }

    public function disconnect()
    {
        $this->logger->info( 'disconnected' );
        $this->client->disconnect();
    }

    /**
     * @param \WebSocketClient $client
     */
    public function setClient(\WebSocketClient $client)
    {
        $this->client = $client;
    }
}