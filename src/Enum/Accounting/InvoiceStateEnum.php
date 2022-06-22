<?php

namespace App\Enum\Accounting;

use App\Entity\Accounting\InvoiceState;
use App\Enum\AppColorEnum;
use App\Enum\DataInitializerInterface;

class InvoiceStateEnum implements DataInitializerInterface
{
    public const DRAFT          = 'draft';
    public const TO_VALIDATE    = 'to_validate';
    public const VALIDATED      = 'validated';
    public const REFUSED        = 'refused';
    public const PAYED          = 'payed';

    public function getData(): array
    {
        return [
            new InvoiceState(self::DRAFT, 'brouillon', color: AppColorEnum::SECONDARY),
            new InvoiceState(self::TO_VALIDATE, 'à valider', color: AppColorEnum::WARNING),
            new InvoiceState(self::VALIDATED, 'valider', color: AppColorEnum::PRIMARY),
            new InvoiceState(self::REFUSED, 'refusé', color: AppColorEnum::DANGER),
            new InvoiceState(self::PAYED, 'payé', color: AppColorEnum::SUCCESS),
        ];
    }

    public function getEnum(): string
    {
        return InvoiceState::class;
    }
}