<?php

namespace App\EventListener\Category\Shopping;


readonly class CreateShoppingCategoryCommand
{
    public function __construct(private string $content)
    {
    }

    public function content():string
    {
        return $this->content;
    }
}