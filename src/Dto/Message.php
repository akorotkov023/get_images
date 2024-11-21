<?php

namespace App\Dto;

class Message
{
    public function __construct(private string $text){}

    public function getText(): string
    {
        return $this->text;
    }
}
