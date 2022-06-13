<?php

namespace App\Enum\Accounting;

use App\Entity\Accounting\PaymentMethod;
use App\Enum\DataInitializerInterface;

class PaymentMethodEnum implements DataInitializerInterface
{
    public const CHEQUE     = 'cheque';
    public const CASH       = 'cash';
    public const BANK_CARD  = 'bank_card';

    public function getData(): array
    {
        return [
            new PaymentMethod(self::CHEQUE, 'Chèque'),
            new PaymentMethod(self::CASH, 'Espèces'),
            new PaymentMethod(self::BANK_CARD, 'Carte bancaire'),
        ];
    }

    public function getEnum(): string
    {
        return PaymentMethod::class;
    }
}