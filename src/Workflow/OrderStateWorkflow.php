<?php

namespace App\Workflow;

use App\Enum\Accounting\InvoiceStateEnum;

class OrderStateWorkflow
{
    public const NAME = 'order_validation';

    //transitions
    public const TO_VALIDATE    = 'to_validate';
    public const VALIDATE       = 'validate';
    public const REJECT         = 'reject';
    public const DELIVERED      = 'delivered';

}