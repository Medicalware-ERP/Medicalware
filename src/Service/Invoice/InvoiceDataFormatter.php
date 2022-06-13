<?php

declare(strict_types=1);

namespace App\Service\Invoice;

use App\Entity\Accounting\Invoice;
use App\Entity\EntityInterface;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class InvoiceDataFormatter implements DataFormatterInterface
{
    public function __construct(private readonly Environment $environment)
    {
    }

    /**
     * @param EntityInterface|Invoice $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[ArrayShape(['reference' => "string", 'patient' => "string", 'date' => "string", 'price' => "string", 'state' => "string", 'actions' => "string"])]
    public function format(EntityInterface|Invoice $data): array
    {
        return [
            'reference' => $this->environment->render('invoice/datatable/columns/reference.html.twig', [
                'invoice' => $data
            ]),
            'patient' => $this->environment->render('invoice/datatable/columns/patient.html.twig', [
                'invoice' => $data
            ]),
            'date' => $this->environment->render('invoice/datatable/columns/date.html.twig', [
                'invoice' => $data
            ]),
            'price' => $this->environment->render('invoice/datatable/columns/price.html.twig', [
                'invoice' => $data
            ]),
            'state' => $this->environment->render('invoice/datatable/columns/state.html.twig', [
                'invoice' => $data
            ]),
            'actions' => $this->environment->render('invoice/datatable/columns/actions.html.twig', [
                'invoice' => $data
            ]),
        ];
    }
}