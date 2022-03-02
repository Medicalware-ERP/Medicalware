<?php

declare(strict_types=1);

namespace App\Enum;

interface DataInitializerInterface
{
    public function getData(): array;

    public function getEnum(): string;
}