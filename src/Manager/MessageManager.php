<?php

namespace App\Manager;

use App\Entity\MessageKafka;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

readonly class MessageManager
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createMessage(string $text): MessageKafka
    {
        $date = new DateTime('now');

        $message = new MessageKafka();
        $message->setText($text);
        $message->setCreatedAt($date);
        $message->setUpdatedAt($date);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }
}
