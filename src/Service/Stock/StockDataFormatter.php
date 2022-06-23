<?php

namespace App\Service\Stock;

use App\Entity\EntityInterface;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class StockDataFormatter implements DataFormatterInterface
{
    public function __construct(private readonly Environment $environment)
    {
    }

    /**
     * @param EntityInterface $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[ArrayShape(['reference' => "string", 'name' => "string", 'price' => "string", 'quantity' => "string", 'provider' => "string", 'actions' => "string"])]
    public function format(EntityInterface $data): array
    {
        return [
            'reference' => $this->environment->render('stock/datatable/columns/reference.html.twig', [
                'stock' => $data
            ]),
            'name' => $this->environment->render('stock/datatable/columns/name.html.twig', [
                'stock' => $data
            ]),
            'price' => $this->environment->render('stock/datatable/columns/price.html.twig', [
                'stock' => $data
            ]),
            'quantity' => $this->environment->render('stock/datatable/columns/quantity.html.twig', [
                'stock' => $data
            ]),
            'services' => $this->environment->render('stock/datatable/columns/services.html.twig', [
                'stock' => $data
            ]),
            'provider' => $this->environment->render('stock/datatable/columns/provider.html.twig', [
                'stock' => $data
            ]),
            'actions' => $this->environment->render('stock/datatable/columns/actions.html.twig', [
                'stock' => $data
            ]),
        ];
    }
}