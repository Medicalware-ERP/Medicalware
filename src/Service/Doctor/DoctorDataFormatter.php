<?php

namespace App\Service\Doctor;

use App\Entity\Doctor;
use App\Entity\EntityInterface;
use App\Service\DataFormatterInterface;
use JetBrains\PhpStorm\ArrayShape;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DoctorDataFormatter implements DataFormatterInterface
{
    public function __construct(private Environment $environment)
    {
    }

    /**
     * @param Doctor $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[ArrayShape(['avatar' => "string", 'firstName' => "string", 'lastName' => "string", 'phone' => "string", 'email' => "string", 'profession' => "string", 'active' => "string", 'specialisation' => "string", 'actions' => "string"])]
    public function format(EntityInterface $data): array
    {
        return [
            'avatar' => $this->environment->render('doctor/datatable/columns/avatar.html.twig', [
                'doctor' => $data
            ]),
            'lastName' => $this->environment->render('doctor/datatable/columns/lastName.html.twig', [
                'doctor' => $data
            ]),
            'firstName' => $this->environment->render('doctor/datatable/columns/firstName.html.twig', [
                'doctor' => $data
            ]),
            'phone' => $this->environment->render('doctor/datatable/columns/phone.html.twig', [
                'doctor' => $data
            ]),
            'email' => $this->environment->render('doctor/datatable/columns/email.html.twig', [
                'doctor' => $data
            ]),
            'profession' => $this->environment->render('doctor/datatable/columns/profession.html.twig', [
                'doctor' => $data
            ]),
            'specialisation' => $this->environment->render('doctor/datatable/columns/specialisation.html.twig', [
                'doctor' => $data
            ]),
            'active' => $this->environment->render('doctor/datatable/columns/active.html.twig', [
                'doctor' => $data
            ]),
            'actions' => $this->environment->render('doctor/datatable/columns/actions.html.twig', [
                'doctor' => $data
            ]),
        ];
    }
}