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

    public const VALUES = [
        self::TVA_20    => 1.2,
        self::TVA_10    => 1.1,
        self::TVA_5     => 1.05,
        self::TVA_0     => 1,
    ];

    #[Pure]
    public function getData(): array
    {
        return [
            new Tva(self::TVA_20, "20%"),
            new Tva(self::TVA_10, "10%"),
            new Tva(self::TVA_5, "5%"),
            new Tva(self::TVA_0, "0%")
        ];
    }

    public function getEnum(): string
    {
        return Tva::class;
    }
}