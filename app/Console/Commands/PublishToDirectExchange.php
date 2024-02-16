<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PublishToDirectExchange extends Command
{
    protected $signature = 'rabbitmq:publish-direct {routingKey} {message}';

    protected $description = 'Publish a message to a direct exchange on RabbitMQ with a routing key';

    public function handle()
    {
        $routingKey = $this->argument('routingKey');
        $messageBody = $this->argument('message');
        $exchange = 'direct_exchange';

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'direct', false, false, false);

        $msg = new AMQPMessage($messageBody);
        $channel->basic_publish($msg, $exchange, $routingKey);

        $this->info(" [x] Sent '$messageBody' with routing key '$routingKey'");

        $channel->close();
        $connection->close();
    }
}
