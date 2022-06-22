<?php

namespace App\Workflow;

use App\Enum\Accounting\InvoiceStateEnum;

class InvoiceStateWorkflow
{
    public const NAME = 'invoice_validation';

    //transitions
    public const TO_VALIDATE    = 'to_validate';
    public const VALIDATE       = 'validate';
    public const REJECT         = 'reject';
    public const PAYED          = 'payed';

}