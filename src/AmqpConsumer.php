<?php

namespace mbfisher\ReactAmqp;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use PhpAmqpLib\Channel\ChannelInterface;
use PhpAmqpLib\Message\AMQPMessage as AmqpMessage;

class AmqpConsumer extends EventEmitter
{
    private $channel;
    private $loop;

    public function __construct(ChannelInterface $channel, LoopInterface $loop)
    {
        $this->channel = $channel;
        $this->loop = $loop;

        $this->getLoop()->addReadStream($channel->getConnection()->getSocket(), function () use ($channel) {
            $channel->wait(null, true);
        });
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function consume($queue)
    {
        /*
         * queue: Queue from where to get the messages
         * consumer_tag: Consumer identifier
         * no_local: Don't receive messages published by this consumer.
         * no_ack: Tells the server if the consumer will acknowledge the messages.
         * exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
         * nowait:
         * callback: A PHP Callback
         */
        $this->getChannel()->basic_consume(
            $queue,
            null,
            false,
            false,
            false,
            false,
            [$this, 'handleMessage']
        );
    }
    
    public function handleMessage(AmqpMessage $message)
    {
        $this->emit('message', $message);
    }
}
