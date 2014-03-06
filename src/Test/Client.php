<?php

namespace Test;

class Client extends AbstractClient implements \WebSocketClient\WebSocketClientInterface
{
    public function onWelcome(array $data)
    {
        parent::onWelcome( $data );

        $this->subscribe( 'public' );
        $this->publish( 'public' , 'hailing frequencies open' );
    }

    public function onEvent($topic, $message)
    {
        parent::onEvent( $topic , $message );
        $this->loop->stop();
    }
}