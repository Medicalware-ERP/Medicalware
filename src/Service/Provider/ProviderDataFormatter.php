<?php

namespace App\Service\Provider;

use App\Entity\EntityInterface;
use App\Entity\Provider;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProviderDataFormatter implements DataFormatterInterface
{
    public function __construct(private readonly Environment $environment)
    {
    }

    /**
     * @param EntityInterface|Provider $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[ArrayShape(['name' => "string", 'phone' => "string", 'email' => "string", 'address' => "string", 'actions' => "string"])]
    public function format(EntityInterface|Provider $data): array
    {
        return [
            'name' => $this->environment->render('provider/datatable/columns/name.html.twig', [
                'provider' => $data
            ]),
            'phone' => $this->environment->render('provider/datatable/columns/phone.html.twig', [
                'provider' => $data
            ]),
            'email' => $this->environment->render('provider/datatable/columns/email.html.twig', [
                'provider' => $data
            ]),
            'address' => $this->environment->render('provider/datatable/columns/address.html.twig', [
                'provider' => $data
            ]),
            'actions' => $this->environment->render('provider/datatable/columns/actions.html.twig', [
                'provider' => $data
            ]),
        ];
    }
}