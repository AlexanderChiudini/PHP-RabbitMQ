# PHP-RabbitMQ

A simple example using command line to show how RabbitMQ exchanges work

For a better insight into RabbitMQ, you can visit [RabbitMQ Simulator](https://tryrabbitmq.com/)

- Exchanges
  - Fanout: When a message is sent to a Fanout exchange, it is copied and delivered to all queues linked to that exchange, without considering the routing key.
  - Direct: It routes the message to the queues based on the exact match between the message routing key and the queue binding key.
  - Topic: The Topic exchange allows for more flexible routing of messages based on matching patterns between the message routing key and the queue binding keys. Supports wildcard characters, allowing subscription to specific topics of interest.

## How it work

- Create the laravel server
  - `php artisan serve --port optional`
- Run docker to have a working instance of RabbitMQ
  - `docker-compose up -d`
- Let your consumers 'listening' the queues
  - `php artisan rabbitmq:consume-fanout`
  - `php artisan rabbitmq:consume-direct {routingKey}`
  - `php artisan rabbitmq:consume-topic {routingKey}`
- Now you can send the messages to their respective exchances. You can do this in two ways:
  - Using command line
    - `php artisan rabbitmq:publish-fanout {message}`
    - `php artisan rabbitmq:publish-direct {routingKey} {message}`
    - `php artisan rabbitmq:publish-topic {routingKey} {message}`
  - Using Postman (or similar)
    - `http://127.0.0.1/api/add-queue-fanout?message=`
    - `http://127.0.0.1/api/add-queue-direct?message=`
    - `http://127.0.0.1/api/add-queue-topic?routingKey=&message=`