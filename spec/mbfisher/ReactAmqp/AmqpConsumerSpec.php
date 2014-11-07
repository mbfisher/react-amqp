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

    function it_adds_a_read_stream($socket, ChannelInterface $channel, ConnectionInterface $connection, LoopInterface $loop)
    {
        $channel->getConnection()->willReturn($connection);
        $connection->getSocket()->willReturn($socket);

        $loop->addReadStream($socket, [$this, 'wait'])->shouldBeCalled();

        $this->beConstructedWith($channel, $loop);
    }

    function it_waits_for_data(ChannelInterface $channel)
    {
        $channel->wait(null, true)->shouldBeCalled();
        $this->wait();
    }

    function it_consumes_a_queue(ChannelInterface $channel)
    {
        $queue = 'test';

        $channel->basic_consume(
            $queue,
            null,
            false,
            false,
            false,
            false,
            [$this, 'handleMessage']
        )->shouldBeCalled();

        $this->consume($queue);
    }
}
