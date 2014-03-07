<?php
namespace Test;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\Wamp\WampServerInterface;
use Psr\Log\LoggerInterface,
    Psr\Log\LoggerAwareInterface,
    Psr\Log\LoggerAwareTrait;

/**
 * A simple pub/sub implementation
 * Anything clients publish on a topic will be received
 *  on that topic by all clients
 */
class PubSub implements WampServerInterface , LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct( LoggerInterface $logger = null )
    {
        if( $logger )
        {
            $this->setLogger( $logger );
        }
    }

    protected function log( $msg )
    {
        if( $this->logger )
        {
            $this->logger->info( $msg );
        }
        else
        {
            echo "{$msg}\n";
        }
    }

    public function onPublish(Conn $conn, $topic, $event, array $exclude = array(), array $eligible = array()) {
        $topic->broadcast($event);

        $this->log( "Connection {$conn->resourceId} published message '{$event}' on topic {$topic}" );
        $this->log( "Broadcasted to {$topic->count()} subscribers" );
    }

    public function onCall(Conn $conn, $id, $topic, array $params)
    {
        $conn->callError($id, $topic, 'RPC not supported');
    }

    public function onOpen(Conn $conn)
    {
        $this->log( "New connection! ({$conn->resourceId})" );
    }

    public function onClose(Conn $conn)
    {
        $this->log( "Connection {$conn->resourceId} has disconnected" );
    }

    public function onSubscribe(Conn $conn, $topic)
    {
        $topic->add( $conn );

        $this->log( "Connection {$conn->resourceId} subscribed to topic {$topic}" );
        $this->log( "Topic {$topic} has {$topic->count()} subscribers" );
    }

    public function onUnSubscribe(Conn $conn, $topic)
    {
        $topic->remove( $conn );

        $this->log( "Connection {$conn->resourceId} unsubscribed from topic {$topic}" );
    }

    public function onError(Conn $conn, \Exception $e)
    {
        $this->log( "Error on connection {$conn->resourceId}: {$e->getMessage()}" );
    }
}