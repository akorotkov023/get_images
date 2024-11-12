<?php

namespace App\Service\Redis;

interface ConnectorFacadeInterface
{
    public function getCard();
    public function setCard();
}
