<?php

namespace App\Tests\Service\Redis;

use App\Service\Redis\Connector;
use App\Service\Redis\ConnectorFacade;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ConnectorFacadeTest extends TestCase
{
    private LoggerInterface $logger;
    private Connector $redisConnector;
    private ConnectorFacade $connectorFacade;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->redisConnector = $this->createMock(Connector::class);
        $this->connectorFacade = new ConnectorFacade($this->logger, $this->redisConnector);
    }

    public function testGetArticleReturnsData()
    {
        $id = '123';
        $expectedValue = 'Sample Article';

        // Настройка мока для метода getConnect
        $this->redisConnector->expects($this->once())
            ->method('getConnect');

        // Настройка мока для метода getValue
        $this->redisConnector->expects($this->once())
            ->method('getValue')
            ->with($id)
            ->willReturn($expectedValue);

        // Настройка мока для метода info
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Значение = ' . $expectedValue);

        // Вызов метода
        $result = $this->connectorFacade->getArticle($id);

        // Проверка результата
        $this->assertEquals($expectedValue, $result);
    }

    public function testGetArticleReturnsNullWhenValueIsNull()
    {
        $id = '123';

        // Настройка мока для метода getConnect
        $this->redisConnector->expects($this->once())
            ->method('getConnect');

        // Настройка мока для метода getValue, чтобы вернуть null
        $this->redisConnector->expects($this->once())
            ->method('getValue')
            ->with($id)
            ->willReturn(null);

        // Настройка мока для метода info
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Значение = ' . null);

        // Вызов метода
        $result = $this->connectorFacade->getArticle($id);

        // Проверка результата
        $this->assertNull($result);
    }

    //TODO testSetCardLogsMessage

    public function testSetArticleLogsInfo()
    {
        $key = 'article:123';
        $value = [
            'title' => 'Test Title',
            'text' => 'Test Text',
            'rating' => 5,
        ];

        // Настройка мока для метода getConnect
        $this->redisConnector->expects($this->once())
            ->method('getConnect');

        // Настройка мока для метода setCard
        $this->redisConnector->expects($this->once())
            ->method('setCard')
            ->with($key, $value);

        // Настройка мока для метода info
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Запись добавлена ' . $key);

        // Вызов метода
        $this->connectorFacade->setArticle($key, $value);
    }
}
