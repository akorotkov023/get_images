<?php

namespace App\Tests\Controller;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{

    /**
     * @dataProvider dataProvider
     */
    #[NoReturn]
    public function testMethodOnRoute(string $uri, string $method): void
    {
//        dd($method);
        // Создаем клиент для отправки запросов
        $client = static::createClient();
        if ($method === 'GET') {
            // Отправляем GET-запрос на нужный маршрут
            $client->request($method, $uri);
        } else {
            // Подготавливаем данные для POST запроса
            $data = json_encode(['url' => 'http://example.com']);
            // Отправляем POST запрос
            $client->request($method, $uri, [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        }
        // Проверяем, что ответ имеет статус 200
        $this->assertResponseIsSuccessful();
        // Дополнительно можно проверить, что ответ содержит ожидаемые данные
//        $this->assertSelectorTextContains('h2', 'Поиск картинок на сайте');
    }

    public static function dataProvider(): array
    {
        return [
            ['/', 'GET'],
            ['/test', 'GET'],
            ['/url', 'POST'],
            ['/error', 'POST'],
        ];
    }

}
