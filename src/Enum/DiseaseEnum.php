<?php

declare(strict_types=1);

namespace App\Enum;


use App\Entity\Disease;
use JetBrains\PhpStorm\Pure;

class DiseaseEnum implements DataInitializerInterface
{
    public const TUBERCULOSE = 'Tuberculose';
    public const LEUCEMIE = 'Leucémie';
    public const PALUDISME = 'Paludisme';
    public const COVID_19  = 'COVID_19';
    public const EBOLA  = 'Ebola';

    #[Pure]
    public function getData(): array
    {
        return [
            new Disease(self::TUBERCULOSE, "Tuberculose"),
            new Disease(self::LEUCEMIE, "Leucémie"),
            new Disease(self::PALUDISME, "Paludisme"),
            new Disease(self::COVID_19, "Covid 19"),
            new Disease(self::EBOLA, "Ebola"),
        ];
    }

    public function getEnum(): string
    {
        return Disease::class;
    }
}