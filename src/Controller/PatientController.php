<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Service\Patient\PatientDataFormatter;
use App\Service\User\UserDataFormatter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends BaseController
{
    #[Route('/patients', name: 'app_patients')]
    public function index(): Response
    {
        return $this->render('patient/index.html.twig');
    }

    /**
     * @param Request $request
     * @param PatientDataFormatter $patientDataFormatter
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/patientsJson', name: 'patients_json')]
    public function paginate(Request $request, PatientDataFormatter $patientDataFormatter): JsonResponse
    {
        return $this->paginateRequest(Patient::class, $request, $patientDataFormatter);
    }
}
