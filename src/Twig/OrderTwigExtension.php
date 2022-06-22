<?php

namespace App\Twig;

use App\Workflow\InvoiceStateWorkflow;
use App\Workflow\OrderStateWorkflow;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class OrderTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
       return [
           new TwigFilter('translate_order_workflow', $this->translateOrderWorkflow(...))
       ];
    }

    public function translateOrderWorkflow(string $transition): string
    {
       return match ($transition) {
           OrderStateWorkflow::TO_VALIDATE => 'A valider',
           OrderStateWorkflow::VALIDATE   => 'Valider',
           OrderStateWorkflow::REJECT     => 'Refusé',
           OrderStateWorkflow::DELIVERED  => 'Livré',
        };
    }
}