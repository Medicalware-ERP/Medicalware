<?php

declare(strict_types=1);

namespace App\Enum;


use App\Entity\Room\RoomType;
use JetBrains\PhpStorm\Pure;

class RoomOptionEnum implements DataInitializerInterface
{
    public const TV = 'Television';
    public const BATHROOM = 'Bathroom';

    public const WATER_DISPENSER = 'Water dispender';

    public const PROJECTOR = 'Projector';
    public const WHITE_BOARD = 'White Board';
    public const ELECTRONIC_BOARD = 'Electronic Board';

    #[Pure]
    public function getData(): array
    {
        return [
            new RoomType(self::TV, "Télévision"),
            new RoomType(self::BATHROOM, "Salle de bain"),

            new RoomType(self::WATER_DISPENSER, "Fontaine à eau"),

            new RoomType(self::PROJECTOR, "Rétroprojecteur"),
            new RoomType(self::WHITE_BOARD, "Tableau blanc"),
            new RoomType(self::ELECTRONIC_BOARD, "Tableau électronique"),
        ];
    }

    public function getEnum(): string
    {
        return RoomType::class;
    }
}