<?php

namespace App\EventListener\Product\Shopping;


readonly class CreateShoppingProductCommand
{
    public function __construct(private string $content)
    {
    }

    public function content():string
    {
        return $this->content;
    }
}