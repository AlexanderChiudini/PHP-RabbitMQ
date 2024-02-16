<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PublishToFanoutExchange extends Command
{
    protected $signature = 'rabbitmq:publish-fanout {message}';

    protected $description = 'Publish a message to a fanout exchange on RabbitMQ';

    public function handle()
    {
        $messageBody = $this->argument('message');
        $exchange = 'fanout_exchange';

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'fanout', false, false, false);

        $msg = new AMQPMessage($messageBody);
        $channel->basic_publish($msg, $exchange);

        $this->info(" [x] Sent '$messageBody'");

        $channel->close();
        $connection->close();
    }
}
