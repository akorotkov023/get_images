<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\Redis\ConnectorFacade;
use App\Service\Redis\ConnectorFacadeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RedisController extends AbstractController
{
    #[Route('/redis_check', name: 'app_redis_check')]
    public function check(ConnectorFacadeInterface $connectorFacade): JsonResponse
    {
        $id = '1';
//        $data = $connectorFacade->getCard($id);

//        dd($data);
        // Формируем данные для ответа

        $res = [];

        return new JsonResponse($res);
    }

    #[Route('/trending', name: 'app_trending')]
    public function random(EntityManagerInterface $entityManager): JsonResponse
    {
        // Получаем все статьи
        $articles = $entityManager->getRepository(Article::class)->findAll();
        // Проверяем, есть ли статьи
        if (empty($articles)) {
            return new JsonResponse(['error' => 'No articles found'], Response::HTTP_NOT_FOUND);
        }

        // Выбираем случайную статью
        $randomArticle = $articles[array_rand($articles)];

        // Формируем данные для ответа
        $data = [
            'id' => $randomArticle->getId(),
            'title' => $randomArticle->getTitle(),
            'text' => $randomArticle->getText(),
            'rating' => $randomArticle->getRating(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/article/{id}', name: 'app_article')]
    public function show(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Получаем статью по ID
        $article = $entityManager->getRepository(Article::class)->find($id);

        // Проверяем, существует ли статья
        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        // Формируем данные для ответа
        $data = [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'text' => $article->getText(),
            'rating' => $article->getRating(),
        ];

        return new JsonResponse($data);
    }
}
