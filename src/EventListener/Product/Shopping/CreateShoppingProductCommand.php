<?php

namespace App\EventListener\Product\Shopping;


class CreateShoppingProductCommand
{
    public function __construct(private readonly string $content)
    {
    }

    public function content():string
    {
        return $this->content;
    }
}