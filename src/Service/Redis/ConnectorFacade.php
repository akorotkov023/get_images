<?php

namespace App\Service\Redis;

use Psr\Log\LoggerInterface;

class ConnectorFacade implements ConnectorFacadeInterface
{
    private LoggerInterface $logger;
    private Connector $redisConnector;

    /**
     * @param LoggerInterface $logger
     * @param Connector $redisConnector
     */
    public function __construct(LoggerInterface $logger, Connector $redisConnector) {
        $this->logger = $logger;
        $this->redisConnector = $redisConnector;
    }

    public function getArticle(string $id): ?array
    {
        $this->redisConnector->getConnect();
        $value = $this->redisConnector->getValue($id);
        $this->logger->info('Значение = ' . $value);
        if (!isset($value)) {
            return null;
        }
        //TODO переделать логику
        $res = json_decode($value);
        return [
            'id' => $value,
            'title' => $res->title,
            'text' => $res->text,
            'rating' => $res->rating,
        ];
    }

    public function setArticle(string $key, array $value): void
    {
        $this->redisConnector->getConnect();

        $this->redisConnector->setCard($key, $value);
        $this->logger->info('Запись добавлена ' . $key);

    }
}
