<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RedisController extends AbstractController
{
    #[Route('/redis', name: 'app_redis')]
    public function index(): Response
    {
        return $this->render('redis/index.html.twig', [
            'controller_name' => 'RedisController',
        ]);
    }
}
