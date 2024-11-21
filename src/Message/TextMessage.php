<?php

namespace App\Message;

final class TextMessage
{
    public function __construct(private string $text){}

    public function getText(): string
    {
        return $this->text;
    }
}
