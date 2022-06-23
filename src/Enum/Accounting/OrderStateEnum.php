<?php

namespace App\Enum\Accounting;

use App\Entity\Accounting\OrderState;
use App\Enum\AppColorEnum;
use App\Enum\DataInitializerInterface;

class OrderStateEnum implements DataInitializerInterface
{
    public const DRAFT          = 'draft';
    public const TO_VALIDATE    = 'to_validate';
    public const VALIDATED      = 'validated';
    public const REFUSED        = 'refused';
    public const DELIVERY       = 'delivery';

    public function getData(): array
    {
        return [
            new OrderState(self::DRAFT, 'brouillon', color: AppColorEnum::SECONDARY),
            new OrderState(self::TO_VALIDATE, 'à valider', color: AppColorEnum::WARNING),
            new OrderState(self::VALIDATED, 'valider', color: AppColorEnum::PRIMARY),
            new OrderState(self::REFUSED, 'refusé', color: AppColorEnum::DANGER),
            new OrderState(self::DELIVERY, 'livré', color: AppColorEnum::SUCCESS),
        ];
    }

    public function getEnum(): string
    {
        return OrderState::class;
    }
}