<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PublishToTopicExchange extends Command
{
    protected $signature = 'rabbitmq:publish-topic {routingKey} {message}';

    protected $description = 'Publish a message to a topic exchange on RabbitMQ with a routing key';

    public function handle()
    {
        $routingKey = $this->argument('routingKey');
        $messageBody = $this->argument('message');
        $exchange = 'topic_exchange';

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'topic', false, false, false);

        $msg = new AMQPMessage($messageBody);
        $channel->basic_publish($msg, $exchange, $routingKey);

        $this->info(" [x] Sent '$messageBody' with routing key '$routingKey'");

        $channel->close();
        $connection->close();
    }
}
