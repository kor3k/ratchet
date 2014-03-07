<?php

namespace Test;

class Client extends AbstractClient
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