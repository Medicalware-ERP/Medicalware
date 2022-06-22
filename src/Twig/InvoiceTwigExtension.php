<?php

namespace App\Twig;

use App\Workflow\InvoiceStateWorkflow;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class InvoiceTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
       return [
           new TwigFilter('translate_invoice_workflow', $this->translateInvoiceWorkflow(...))
       ];
    }

    public function translateInvoiceWorkflow(string $transition): string
    {
       return match ($transition) {
           InvoiceStateWorkflow::TO_VALIDATE => 'A valider',
           InvoiceStateWorkflow::VALIDATE   => 'Valider',
           InvoiceStateWorkflow::REJECT     => 'Refusé',
           InvoiceStateWorkflow::PAYED      => 'Payé',
        };
    }
}