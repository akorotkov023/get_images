### Добавил Redis Client

Загрузка фикстур в таблцу article
```bash
php bin/console doctrine:fixtures:load
```

Запуск для проверки в консоле
```bash 
curl --location 'http://localhost:7777/article/148'
curl --location 'http://localhost:7777/trending'
```

Выборка статей первоначально идет из базы, но при повторном запросе той же статьи из Redis
тем самым значительно ускоряем её отображение.

### Добавил Kafka

web: http://localhost:9900/

Добавить запись в kafka: http://localhost:7777/kafka/test

#### Запустить консьюмер в докере:
```bash
docker exec -it php bash
php bin/console kafka:consumer:run send_message
```

#### Добавим два consumer group0 group1

```php
    App\Consumer\MessageConsumer0:
        class: App\Consumer\MessageConsumer
        arguments:
            $topic: !php/const App\Service\KafkaService::SEND_MESSAGE_TOPIC
            $groupId: group0
            $name: send_message0

    App\Consumer\MessageConsumer1:
        class: App\Consumer\MessageConsumer
        arguments:
            $topic: !php/const App\Service\KafkaService::SEND_MESSAGE_TOPIC
            $groupId: group1
            $name: send_message1
```

#### Запуск в ручном фоновом режиме

```bash
php bin/console kafka:consumer:run send_message0 &
php bin/console kafka:consumer:run send_message1 &
```

#### Добавление партиции

Заходим в kafka
```bash
docker exec -it kafka sh

/bin/kafka-topics --bootstrap-server localhost:9092 --alter --topic send_message --partitions 2
```

Распределять по партициям
```php
$kafkaService->send(KafkaService::SEND_MESSAGE_TOPIC, ['text' => $text . $uniqueId . $i], ($i % 2 === 0) ? 0 : 1);
```


### Добавил RabbitMQ

Заходим в контейнер 
```bash 
docker exec -it rabbitmq bash
rabbitmq-plugins enable rabbitmq_management
```
web - http://127.0.0.1:15672/

Publisher - тот кто закидывает сообщения в очередь


Consumer - тот кто принимает сообщения из очереди
