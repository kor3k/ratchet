<?php

require dirname(__DIR__) . '/vendor/autoload.php';

class Client implements \WebSocketClient\WebSocketClientInterface
{
    private $client;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    public function __construct( $logger )
    {
        $this->logger   =   $logger;
    }

    public function onWelcome(array $data)
    {
        $this->logger->info( 'welcome' , $data );
        $this->subscribe( 'kittensCategory' );
        $this->publish( 'kittensCategory' , 'jupijou' );
    }

    public function onEvent($topic, $message)
    {
        $this->logger->info( 'event' , [ 'topic' => $topic , 'message' => $message ] );
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

    public function setClient(WebSocketClient $client)
    {
        $this->client = $client;
    }
}



$logger = new \Monolog\Logger( 'wsclient' );
$logger->pushHandler( new \Monolog\Handler\StreamHandler( dirname(__DIR__).'/logs/dev.log' ) );

$loop = React\EventLoop\Factory::create();
$client = new WebSocketClient( new Client( $logger ) , $loop );

$loop->run();

