<?php

declare(strict_types=1);

namespace App\Enum;

use App\Entity\RoomType;
use JetBrains\PhpStorm\Pure;

class RoomTypeEnum implements DataInitializerInterface
{
    public const CHAMBRE = 'Chamber';
    public const OPERATING = 'Operating';
    public const MEETING = 'Chamber';

    #[Pure]
    public function getData(): array
    {
        return [
            new RoomType(self::CHAMBRE, "Chambre"),
            new RoomType(self::OPERATING, "Bloc opératoire"),
            new RoomType(self::MEETING, "Salle de réunion")
        ];
    }

    public function getEnum(): string
    {
        return RoomType::class;
    }
}