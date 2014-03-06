<?php

namespace Test;

/**
 * Class Pusher
 *
 * pusher relies on echo or ack from server after he publishes, this stops the loop
 * boot() must be called once before first push
 *
 * @package Test
 */
class Pusher extends AbstractClient
{
    public function onWelcome(array $data)
    {
        parent::onWelcome( $data );
        $this->loop->stop();
    }

    public function onEvent($topic, $message)
    {
        $this->unsubscribe( $topic );

        parent::onEvent( $topic , $message );

        $this->loop->stop();
    }

    public function push( $topic , $message )
    {
        $this->subscribe( $topic );
        $this->publish( $topic , $message );

        $this->loop->run();

        return $this;
    }

    public function boot()
    {
        $this->loop->run();

        return $this;
    }
}