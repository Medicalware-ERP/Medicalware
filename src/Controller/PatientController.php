<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\User;
use App\Form\PatientType;
use App\Form\UserType;
use App\Service\Patient\PatientDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends BaseController
{

    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

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

    #[Route('/patient/add', name: 'app_add_patient')]
    public function add(Request $request): Response
    {
        $patient = new Patient();

        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($patient);
            $this->manager->flush();

            return $this->redirectToRoute("app_patients");
        }

        return $this->renderForm('patient/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/patient/edit/{id}', name: 'app_edit_patient')]
    public function edit(Request $request, int $id): Response
    {
        $patient = $this->manager->find(Patient::class, $id) ?? throw new NotFoundHttpException("Patient non trouvé");

        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($patient);
            $this->manager->flush();
            return $this->redirectToRoute("app_patients");
        }

        return $this->renderForm('patient/form.html.twig', [
            'form' => $form,
            'patient' => $patient
        ]);
    }

    #[Route('/patient/{id}', name: 'app_show_patient')]
    public function show(int $id): Response
    {
        $patient = $this->manager->find(Patient::class, $id) ?? throw new NotFoundHttpException("Patient non trouvé");

        return $this->render('patient/show.html.twig', [
            'patient' => $patient
        ]);
    }

}
