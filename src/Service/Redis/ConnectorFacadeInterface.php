<?php

namespace App\Service\Redis;

interface ConnectorFacadeInterface
{
    public function getCard(string $id);
    public function setCard();
}
