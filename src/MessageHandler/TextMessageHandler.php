<?php

namespace App\MessageHandler;

use App\Manager\MessageManager;
use App\Message\TextMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TextMessageHandler
{
    public function __construct(private readonly MessageManager $messageManager)
    {
    }
    public function __invoke(TextMessage $message): void
    {
        $this->messageManager->createMessage($message->getText());
    }
}
