<?php
namespace Test;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\Wamp\WampServerInterface;

/**
 * A simple pub/sub implementation
 * Anything clients publish on a topic will be received
 *  on that topic by all clients
 */
class OpenPubSub implements WampServerInterface {
    public function onPublish(Conn $conn, $topic, $event, array $exclude = array(), array $eligible = array()) {
        $topic->broadcast($event);
        echo "Connection {$conn->resourceId} published message '{$event}' on topic {$topic}\n";
        echo "Broadcasted to {$topic->count()} subscribers\n";
    }

    public function onCall(Conn $conn, $id, $topic, array $params) {
        $conn->callError($id, $topic, 'RPC not supported');
    }

    public function onOpen(Conn $conn)
    {
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onClose(Conn $conn)
    {
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onSubscribe(Conn $conn, $topic)
    {
        $topic->add( $conn );
        echo "Connection {$conn->resourceId} subscribed to topic {$topic}\n";
        echo "Topic {$topic} has {$topic->count()} subscribers\n";
    }

    public function onUnSubscribe(Conn $conn, $topic)
    {
        $topic->remove( $conn );
        echo "Connection {$conn->resourceId} unsubscribed from topic {$topic}\n";
    }

    public function onError(Conn $conn, \Exception $e) {
    }
}