<?php

namespace App\Service\Redis;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Service\Redis\Connector;
use Raketa\BackendTestTask\Service\Redis\ConnectorException;

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

    public function getCard(): string
    {
        $this->redisConnector->getConnect();
        $value = $this->redisConnector->getCard();
        $this->logger->info('Значение = ' . $value);

        return $value;
    }

    /**
     * @throws ConnectorException
     */
    public function setCard(): void
    {
        $this->redisConnector->getConnect();
        $this->redisConnector->setCard('123', []);
        $this->logger->info('Запись добавлена ' . '123');

    }
}
