<?php

namespace App\Service\Patient;

use App\Entity\EntityInterface;
use App\Entity\Patient;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PatientDataFormatter implements DataFormatterInterface
{
    public function __construct(private Environment $environment)
    {
    }

    /**
     * @param Patient $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[ArrayShape(['numberSocialSecurity' => "string", 'avatar' => "string", 'firstName' => "string", 'lastName' => "string", 'phone' => "string", 'email' => "string", 'actions' => "string"])]
    public function format(EntityInterface $data): array
    {
        return [
            'numberSocialSecurity' => $this->environment->render('patient/datatable/columns/numberSocialSecurity.html.twig', [
                'patient' => $data
            ]),
            'avatar' => $this->environment->render('patient/datatable/columns/avatar.html.twig', [
                'patient' => $data
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
            'actions' => $this->environment->render('patient/datatable/columns/actions.html.twig', [
                'patient' => $data
            ]),
        ];
    }
}