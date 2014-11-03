<?php

namespace spec\mbfisher\ReactAmqp;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use PhpAmqpLib\Channel\ChannelInterface;
use PhpAmqpLib\Connection\ConnectionInterface;
use React\EventLoop\LoopInterface;

class AmqpConsumerSpec extends ObjectBehavior
{
    function let(ChannelInterface $channel, ConnectionInterface $connection, LoopInterface $loop)
    {
        $channel->getConnection()->willReturn($connection);

        $this->beConstructedWith($channel, $loop);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('mbfisher\ReactAmqp\AmqpConsumer');
    }
}
