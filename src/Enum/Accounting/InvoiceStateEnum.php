<?php

namespace App\Enum\Accounting;

use App\Entity\Accounting\InvoiceState;
use App\Enum\DataInitializerInterface;

class InvoiceStateEnum implements DataInitializerInterface
{
    public const DRAFT      = 'brouillon';
    public const SUBMITTED  = 'soumise';
    public const PAYED      = 'payed';

    public function getData(): array
    {
        return [
            new InvoiceState(self::DRAFT, 'brouillon'),
            new InvoiceState(self::SUBMITTED, 'soumise'),
            new InvoiceState(self::PAYED, 'payed'),
        ];
    }

    public function getEnum(): string
    {
        return InvoiceState::class;
    }
}