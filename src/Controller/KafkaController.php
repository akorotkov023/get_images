<?php

namespace App\Controller;

use App\Dto\Message;
use App\Manager\MessageManager;
use App\Message\TextMessage;
use App\Service\KafkaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class KafkaController
{
    #[Route('/kafka/database', name: 'app_kafka_database', methods: ['POST'])]
    public function saveMessageDatabase(Request $request, MessageManager $messageManager): JsonResponse
    {
        $text = $request->query->get('text');
        $message = $messageManager->createMessage($text);

        return new JsonResponse(['success' => true, 'messageId' => $message->getId()], Response::HTTP_OK);
    }

    #[Route('/kafka/message', name: 'app_kafka_message')]
    public function saveMessageAction(Request $request, MessageBusInterface $messageBus): JsonResponse
    {
        $text = $request->query->get('text');
        $count = $request->query->get('count');
        for ($i = 0; $i < $count; $i++) {
//            $messageBus->dispatch(new Message($text.' #'.$i));
            $messageBus->dispatch(new TextMessage($text.' #'.$i));
        }

        return new JsonResponse(['success' => true, 'count' => $count], Response::HTTP_OK);
    }

    #[Route('/kafka/test', name: 'app_kafka_test')]
    public function saveTest(KafkaService $kafkaService): JsonResponse
    {
        $kafkaService->send(KafkaService::SEND_MESSAGE_TOPIC, ['text' => 'Test value']);

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }
}
