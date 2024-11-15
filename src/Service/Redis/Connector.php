<?php

namespace App\Service\Redis;
use App\Entity\Article;
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
                echo "Соединение было установлено ранее с Redis." . PHP_EOL;
                return;
            }
            $this->isConnected = true;
            $this->client = new Client([
                'scheme' => 'tcp',
                'host' => $this->host, // Адрес Redis в Docker
                'port' => $this->port, // Порт, на котором работает Redis
            ]);
//            $this->client->auth($this->password);
//            $this->client->select($this->dbIndex);
            $this->client->connect();
            echo "Successfully connected to Redis." . PHP_EOL;
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
            echo "ConnectionException: " . $ex->getMessage() . PHP_EOL;
        }
        return null;
    }


    public function setCard(string $key, array $value): void
    {
        try {
            // Преобразуем массив в JSON
            $jsonData = json_encode($value);
            $this->client->setex($key, 20, $jsonData);
        } catch (ConnectionException $e) {
            echo $e->getMessage();
        }
    }

}
