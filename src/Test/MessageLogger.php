<?php
namespace Test;

use Ratchet\MessageComponentInterface;
use Ratchet\ComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\WebSocket\WsServerInterface;
use Ratchet\Wamp\WampServerInterface;
use Psr\Log\LoggerInterface;

/**
 * A Ratchet component that wraps Monolog loggers tracking received messages
 * @todo Get outgoing working; could create LoggingConnection decorator
 */
class MessageLogger implements MessageComponentInterface , WsServerInterface , WampServerInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Ratchet\ComponentInterface|null
     */
    protected $_component;

    /**
     * Counts the number of open connections
     * @var int
     */
    protected $_i = 0;

    /**
     * @param \Ratchet\ComponentInterface   $component
     * @param \Psr\Log\LoggerInterface      $logger
     */
    public function __construct( ComponentInterface $component , LoggerInterface $logger )
    {
        $this->_component   =   $component;
        $this->logger       =   $logger;
    }

    /**
     * {@inheritdoc}
     */
    function onOpen(ConnectionInterface $conn)
    {
        $this->_i++;

        $this->logger->info('onOpen', array('#open' => $this->_i, 'id' => $conn->resourceId, 'ip' => $conn->remoteAddress));

        $this->_component->onOpen($conn);
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->logger->info('onMsg', array('from' => $from->resourceId, 'len' => strlen($msg), 'msg' => $msg));

        $this->_component->onMessage($from, $msg);
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->_i--;

        $this->logger->info('onClose', array('#open' => $this->_i, 'id' => $conn->resourceId));

        $this->_component->onClose($conn);
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->error("onError: ({$e->getCode()}): {$e->getMessage()}", array('id' => $conn->resourceId, 'file' => $e->getFile(), 'line' => $e->getLine()));

        $this->_component->onError($conn, $e);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubProtocols() {
        if ($this->_component instanceof WsServerInterface) {
            return $this->_component->getSubProtocols();
        } else {
            return array();
        }
    }

    /**
     * An RPC call has been received
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param string                       $id     The unique ID of the RPC, required to respond to
     * @param string|Topic                 $topic  The topic to execute the call against
     * @param array                        $params Call parameters received from the client
     */
    function onCall( ConnectionInterface $conn , $id , $topic , array $params )
    {
        $this->logger->info('onCall', array('from' => $conn->resourceId, 'topic' => (string)$topic , 'method' => $id, 'params' => $params));

        $this->_component->onCall( $conn , $id , $topic , $params );
    }

    /**
     * A request to subscribe to a topic has been made
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param string|Topic                 $topic The topic to subscribe to
     */
    function onSubscribe( ConnectionInterface $conn , $topic )
    {
        $this->logger->info('onSubscribe', array('from' => $conn->resourceId, 'topic' => (string)$topic ));

        $this->_component->onSubscribe( $conn , $topic );
    }

    /**
     * A request to unsubscribe from a topic has been made
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param string|Topic                 $topic The topic to unsubscribe from
     */
    function onUnSubscribe( ConnectionInterface $conn , $topic )
    {
        $this->logger->info('onUnSubscribe', array('from' => $conn->resourceId, 'topic' => (string)$topic ));

        $this->_component->onUnSubscribe( $conn , $topic );
    }

    /**
     * A client is attempting to publish content to a subscribed connections on a URI
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param string|Topic                 $topic    The topic the user has attempted to publish to
     * @param string                       $event    Payload of the publish
     * @param array                        $exclude  A list of session IDs the message should be excluded from (blacklist)
     * @param array                        $eligible A list of session Ids the message should be send to (whitelist)
     */
    function onPublish( ConnectionInterface $conn , $topic , $event , array $exclude , array $eligible )
    {
        $this->logger->info('onPublish', array('from' => $conn->resourceId, 'topic' => (string)$topic , 'event' => $event, 'exclude' => $exclude, 'eligible' => $eligible));

        $this->_component->onPublish( $conn , $topic , $event , $exclude , $eligible );
    }


}