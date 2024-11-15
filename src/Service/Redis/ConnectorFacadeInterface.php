<?php

namespace App\Service\Redis;

interface ConnectorFacadeInterface
{
    public function getArticle(string $id);
    public function setArticle(string $key, array $value);
}
