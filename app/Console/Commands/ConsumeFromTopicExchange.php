<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeFromTopicExchange extends Command
{
    protected $signature = 'rabbitmq:consume-topic {bindingKey}';

    protected $description = 'Consume messages from a topic exchange on RabbitMQ with a binding key';

    public function handle()
    {
        $bindingKey = $this->argument('bindingKey');
        $exchange = 'topic_exchange';

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'topic', false, false, false);

        list($queue_name,,) = $channel->queue_declare("", false, false, true, false);
        $channel->queue_bind($queue_name, $exchange, $bindingKey);

        $this->info(" [*] Waiting for messages for '$bindingKey'. To exit press CTRL+C");

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
