<?php

namespace App\Service\User;

use App\Entity\EntityInterface;
use App\Entity\User;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserDataFormatter implements DataFormatterInterface
{
    public function __construct(private Environment $environment)
    {
    }

    /**
     * @param User $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[ArrayShape(['avatar' => "string", 'firstName' => "string", 'lastName' => "string", 'phone' => "string", 'email' => "string", 'profession' => "string", 'active' => "string", 'actions' => "string"])]
    public function format(EntityInterface $data): array
    {
        return [
            'avatar' => $this->environment->render('human_resources/datatable/columns/avatar.html.twig', [
                'user' => $data
            ]),
            'firstName' => $this->environment->render('human_resources/datatable/columns/firstName.html.twig', [
                'user' => $data
            ]),
            'lastName' => $this->environment->render('human_resources/datatable/columns/lastName.html.twig', [
                'user' => $data
            ]),
            'phone' => $this->environment->render('human_resources/datatable/columns/phone.html.twig', [
                'user' => $data
            ]),
            'email' => $this->environment->render('human_resources/datatable/columns/email.html.twig', [
                'user' => $data
            ]),
            'profession' => $this->environment->render('human_resources/datatable/columns/profession.html.twig', [
                'user' => $data
            ]),
            'active' => $this->environment->render('human_resources/datatable/columns/active.html.twig', [
                'user' => $data
            ]),
            'actions' => $this->environment->render('human_resources/datatable/columns/actions.html.twig', [
                'user' => $data
            ]),
        ];
    }
}