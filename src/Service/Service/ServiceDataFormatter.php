<?php

namespace App\Service\Service;

use App\Entity\EntityInterface;
use App\Entity\Service;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ServiceDataFormatter implements DataFormatterInterface
{
    public function __construct(private Environment $environment)
    {
    }

    /**
     * @param EntityInterface|Service $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[ArrayShape(['name' => "string", 'description' => "string", 'actions' => "string"])]
    public function format(EntityInterface|Service $data): array
    {
        return [
            'name' => $this->environment->render('service/datatable/columns/name.html.twig', [
                'service' => $data
            ]),
            'description' => $this->environment->render('service/datatable/columns/description.html.twig', [
                'service' => $data
            ]),
            'actions' => $this->environment->render('service/datatable/columns/actions.html.twig', [
                'service' => $data
            ]),
        ];
    }
}