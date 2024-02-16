<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeFromDirectExchange extends Command
{
    protected $signature = 'rabbitmq:consume-direct {routingKey}';

    protected $description = 'Consume messages from a direct exchange on RabbitMQ with a routing key';

    public function handle()
    {
        $routingKey = $this->argument('routingKey');
        $exchange = 'direct_exchange';

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'direct', false, false, false);

        list($queue_name) = $channel->queue_declare("", false, false, true, false);
        $channel->queue_bind($queue_name, $exchange, $routingKey);

        $this->info(" [*] Waiting for messages in '$queue_name'. To exit press CTRL+C");

        $callback = function ($msg) {
            $this->info(" [x] Received '{$msg->body}'");
        };

        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
