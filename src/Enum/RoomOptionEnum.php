<?php

declare(strict_types=1);

namespace App\Enum;

use App\Entity\Room\RoomOption;
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
            new RoomOption(self::TV, "Télévision"),
            new RoomOption(self::BATHROOM, "Salle de bain"),

            new RoomOption(self::WATER_DISPENSER, "Fontaine à eau"),

            new RoomOption(self::PROJECTOR, "Rétroprojecteur"),
            new RoomOption(self::WHITE_BOARD, "Tableau blanc"),
            new RoomOption(self::ELECTRONIC_BOARD, "Tableau électronique"),
        ];
    }

    public function getEnum(): string
    {
        return RoomOption::class;
    }
}