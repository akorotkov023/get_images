<?php

namespace App\Controller;

use App\Manager\MessageManager;
use App\Service\KafkaService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
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
    public function saveMessageAction(Request $request, KafkaService $kafkaService): JsonResponse
    {
        $text = $request->query->get('text');
        $count = $request->query->get('count');

        $uniqueId = uniqid(' prefix_', true);
        for ($i = 0; $i < $count; $i++) {
            $kafkaService->send(KafkaService::SEND_MESSAGE_TOPIC, ['text' => $text . $uniqueId . $i]);
//            $messageBus->dispatch(new TextMessage($text.' #'.$i));
        }

        return new JsonResponse(['success' => true, 'count' => $count], Response::HTTP_OK);
    }

    #[Route('/kafka/test', name: 'app_kafka_test')]
    public function saveTest(KafkaService $kafkaService): JsonResponse
    {
        $uniqueId = uniqid('prefix_', true);
        $kafkaService->send(KafkaService::SEND_MESSAGE_TOPIC, ['text' => 'Test value with ' . $uniqueId]);

        return new JsonResponse(['success' => true, 'Запись добавлена' => $uniqueId], Response::HTTP_OK);
    }
}
