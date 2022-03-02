<?php

declare(strict_types=1);

namespace App\Enum;


use App\Entity\Tva;
use JetBrains\PhpStorm\Pure;

class TvaEnum implements DataInitializerInterface
{
    public const TVA_20 = 'tva_20';
    public const TVA_10 = 'tva_10';
    public const TVA_5 = 'tva_5';
    public const TVA_0  = 'tva_0';

    #[Pure]
    public function getData(): array
    {
        return [
            new Tva(self::TVA_20, "Tva 20%"),
            new Tva(self::TVA_10, "Tva 10%"),
            new Tva(self::TVA_5, "Tva 5%"),
            new Tva(self::TVA_0, "Tva 0%")
        ];
    }

    public function getEnum(): string
    {
        return Tva::class;
    }
}