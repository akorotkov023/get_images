<?php

namespace App\Controller;

use App\Entity\Article;
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
        $data = $connectorFacade->getArticle('1');
        if (!isset($data)) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }
        $res = json_decode($data);
        $cartArray = [
            'id' => $res->id,
            'title' => $res->title,
            'text' => $res->text,
            'rating' => $res->rating,
        ];

        return new JsonResponse($cartArray);
    }

    #[Route('/article/{id}', name: 'app_article')]
    public function show(string $id, EntityManagerInterface $entityManager, ConnectorFacadeInterface $connectorFacade): JsonResponse
    {
        // Получаем статью по ID
        $data = $connectorFacade->getArticle($id);
        if (!isset($data)) {
            $article = $entityManager->getRepository(Article::class)->find($id);
            // Проверяем, существует ли статья
            if (!$article) {
                return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
            }
            $key = $article->getId();
            $cartArray = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'text' => $article->getText(),
                'rating' => $article->getRating(),
            ];

            $connectorFacade->setArticle($key, $cartArray);
//            echo "Данные из базы." . PHP_EOL;
            return new JsonResponse($cartArray);
        }
        $res = json_decode($data);
//        dd($res);
        $cartArray = [
            'id' => $res->id,
            'title' => $res->title,
            'text' => $res->text,
            'rating' => $res->rating,
        ];

        return new JsonResponse($cartArray);
    }

    #[Route('/trending', name: 'app_trending')]
    public function random(EntityManagerInterface $entityManager, ConnectorFacadeInterface $connectorFacade): JsonResponse
    {
        // Получаем все статьи
        $articles = $entityManager->getRepository(Article::class)->findAll();
        // Проверяем, есть ли статьи
        if (empty($articles)) {
            return new JsonResponse(['error' => 'No articles found'], Response::HTTP_NOT_FOUND);
        }
        // Выбираем случайную статью
        $randomArticle = $articles[array_rand($articles)];

        $data = [
            'id' => $randomArticle->getId(),
            'title' => $randomArticle->getTitle(),
            'text' => $randomArticle->getText(),
            'rating' => $randomArticle->getRating(),
        ];

        $findRedisArticle = $connectorFacade->getArticle($randomArticle->getId());
        if (!isset($findRedisArticle)) {
            $connectorFacade->setArticle($randomArticle->getId(), $data);
        }
        // Формируем данные для ответа

        return new JsonResponse($data);
    }
}
