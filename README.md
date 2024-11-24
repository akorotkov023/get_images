### Добавил Redis Client



### Добавил Kafka

web: http://localhost:9900/

Добавить запись в kafka: http://localhost:7777/kafka/test

Запустить консьюмер в докере:
```bash

docker exec -it php bash
php bin/console kafka:consumer:run send_message
```
