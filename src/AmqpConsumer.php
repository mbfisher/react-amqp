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

        $this->getLoop()->addReadStream($channel->getConnection()->getSocket(), [$this, 'wait']);
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function wait()
    {
        $this->getChannel()->wait(null, true);
    }

    public function consume($queue)
    {
        /**
         * @param string $queue
         * @param string $consumer_tag
         * @param bool $no_local
         * @param bool $no_ack
         * @param bool $exclusive
         * @param bool $nowait
         * @param null $callback
         * @param null $ticket
         * @param array $arguments
         * @return mixed
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
        $this->emit('message', [$message]);
    }
}
