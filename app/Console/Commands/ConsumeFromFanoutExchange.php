<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeFromFanoutExchange extends Command
{
    protected $signature = 'rabbitmq:consume-fanout';

    protected $description = 'Consume messages from a fanout exchange on RabbitMQ';

    public function handle()
    {
        $exchange = 'fanout_exchange';
        $queue = ''; // Let RabbitMQ generate a unique queue name

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'fanout', false, false, false);

        list($queue_name,,) = $channel->queue_declare($queue, false, false, true, false);
        $channel->queue_bind($queue_name, $exchange);

        $this->info(' [*] Waiting for messages. To exit press CTRL+C');

        $callback = function ($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };

        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
