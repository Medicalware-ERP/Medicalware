<?php

namespace App\Service\Order;

use App\Entity\EntityInterface;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class OrderDataFormatter implements DataFormatterInterface
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
    public function format(EntityInterface $data): array
    {
        return [
            'reference' => $this->environment->render('order/datatable/columns/reference.html.twig', [
                'order' => $data
            ]),
            'provider' => $this->environment->render('order/datatable/columns/provider.html.twig', [
                'order' => $data
            ]),
            'datePlanned' => $this->environment->render('order/datatable/columns/date_planned.html.twig', [
                'order' => $data
            ]),
            'date' => $this->environment->render('order/datatable/columns/date.html.twig', [
                'order' => $data
            ]),
            'state' => $this->environment->render('order/datatable/columns/state.html.twig', [
                'order' => $data
            ]),
            'ttc' => $this->environment->render('order/datatable/columns/ttc.html.twig', [
                'order' => $data
            ]),
            'actions' => $this->environment->render('order/datatable/columns/actions.html.twig', [
                'order' => $data
            ]),
        ];
    }
}