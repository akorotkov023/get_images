<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RabbitController extends AbstractController
{
    #[Route('/rabbit', name: 'app_rabbit')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
    }
}
