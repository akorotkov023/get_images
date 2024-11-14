<?php

namespace App\Service\Redis;
use Predis\Client;
use Predis\Connection\ConnectionException;

class Connector
{
    private string $host;
    private int $port;
    private ?string $password;
    private ?int $dbIndex;
    private bool $isConnected;
    private Client $client;

    /**
     * @param string $host
     * @param int $port
     * @param string|null $password
     * @param int|null $dbIndex
     */
    public function __construct(string $host = '127.0.0.1', int $port = 6379, ?string $password = null, ?int $dbIndex = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbIndex = $dbIndex;
        $this->isConnected = false;
    }

    public function getConnect(): void
    {
        try {
            if ($this->isConnected) {
                echo "Соединение было установлено ранее с Redis." . "<br>";
                return;
            }
            $this->isConnected = true;
            $this->client = new Client([
                'scheme' => 'tcp',
                'host' => $this->host, // Адрес Redis в Docker
                'port' => $this->port, // Порт, на котором работает Redis
            ]);
            $this->client->auth($this->password);
            $this->client->select($this->dbIndex);
            $this->client->connect();
            echo "Successfully connected to Redis." . "<br>";
        } catch (ConnectionException $ex) {
            echo "Could not connect to Redis: " . $ex->getMessage() . "<br>";
        }
    }

    public function getDisconnect(): void {
        try{
            $this->client->disconnect();
            echo "Disconnected from Redis." . "<br>";
        } catch (ConnectionException $ex) {
            echo "ConnectionException: " . $ex->getMessage() . "<br>";
        }
    }

    public function getValue(string $key): ?string
    {
        try{
            return $this->client->get($key);
        } catch (ConnectionException $ex) {
            echo "ConnectionException: " . $ex->getMessage() . "<br>";
        }
        return null;
    }

    public function getCard(string $key): ?string
    {
        try{
            $value = $this->client->get($key);
            echo "Get value with key '$value'." . "<br>";
            return $value;
        } catch (ConnectionException $ex) {
            echo "ConnectionException: " . $ex->getMessage() . "<br>";
        }
        return null;
    }


    public function setCard(string $key, Cart $value): void
    {
        try {
            // Преобразуем объект Cart в массив
            $cartArray = [
                'uuid' => $value->getUuid(),
                'customer' => [
                    'id' => $value->getCustomer()->getId(),
                    'name' => implode(' ', [
                        $value->getCustomer()->getLastName(),
                        $value->getCustomer()->getFirstName(),
                        $value->getCustomer()->getMiddleName(),
                    ]),
                    'email' => $value->getCustomer()->getEmail(),
                ],
                'payment_method' => $value->getPaymentMethod(),
                'items' => array_map(function($item) {
                    return [
                        'uuid' => $item->getUuid(),
                        'price' => $item->getPrice(),
                        'quantity' => $item->getQuantity(),
                        'product_uuid' => $item->getProductUuid(),
                    ];
                }, $value->getItems()),
            ];

            // Преобразуем массив в JSON
            $jsonData = json_encode($cartArray);

            // Сохраняем JSON в Redis с истечением срока действия
            $this->client->setex($key, 24 * 60 * 60, $jsonData);
        } catch (RedisException $e) {
            throw new ConnectorException('ConnectorFacade error', $e->getCode(), $e);
        }
    }

}
