<?php

namespace App\Controller;

use App\Entity\Accounting\Invoice;
use App\Entity\MedicalFile;
use App\Entity\MedicalFileLine;
use App\Entity\Patient;
use App\Entity\Planning\Resource;
use App\Enum\RoleEnum;
use App\Form\AvatarType;
use App\Form\MedicalFileType;
use App\Form\PatientType;
use App\Repository\MedicalFileLineRepository;
use App\Repository\PatientRepository;
use App\Service\Invoice\InvoiceDataFormatter;
use App\Service\Patient\PatientDataFormatter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_DOCTOR')]
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
        $modifier = function(QueryBuilder $queryBuilder){
            $queryBuilder->andWhere("e.isArchived = 0");
        };

        return $this->paginateRequest(Patient::class, $request, $patientDataFormatter, $modifier);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/patient/{id}/invoice/paginate', name: 'patient_invoice_json')]
    public function paginateInvoicesOfPatient(Request $request, InvoiceDataFormatter $dataFormatter, int $id): Response
    {
        $patient = $this->manager->find(Patient::class, $id) ?? throw new NotFoundHttpException("Patient non trouvé");

        $modifier = function(QueryBuilder $queryBuilder) use ($patient) {
            $queryBuilder->andWhere("e.patient=" . $patient->getId());
        };

        return $this->paginateRequest(Invoice::class, $request, $dataFormatter, $modifier);
    }

    #[Route('/patient/add', name: 'app_add_patient')]
    public function add(Request $request): Response
    {
        $patient = new Patient();

        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->manager->persist($patient);
                $this->manager->flush();

                $resource = new Resource();
                $resource->setResourceId($patient->getId());
                $resource->setResourceClass(Patient::class);

                $this->manager->persist($resource);
                $this->manager->flush();

                return $this->redirectToReferer();
            }catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError("Cette email est déjà utilisé"));
                return $this->renderForm('patient/form.html.twig', [
                    'form' => $form
                ]);
            }
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
            try{
                $this->manager->persist($patient);
                $this->manager->flush();

                return $this->redirectToReferer();
            }catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError("Cette email est déjà utilisé"));
                return $this->renderForm('patient/form.html.twig', [
                    'form' => $form
                ]);
            }
        }

        return $this->renderForm('patient/form.html.twig', [
            'form' => $form,
            'patient' => $patient
        ]);
    }

    #[Route('/patient/toArchive/{id}', name: 'app_to_archive_patient')]
    public function archive(int $id): Response
    {
        $patient = $this->manager->find(Patient::class, $id) ?? throw new NotFoundHttpException("Patient non trouvé");

        if ($patient == null){
            throw new NotFoundHttpException();
        }

        $patient->setIsArchived(true);
        $this->manager->persist($patient);
        $this->manager->flush();
        return $this->redirectToRoute("app_patients");
    }

    #[Route('/patient/show/{id}/information', name: 'patient_show_information')]
    public function info(int $id, PatientRepository $patientRepository): Response
    {
        $patient = $patientRepository->find($id);

        return $this->renderForm('patient/includes/_informations.html.twig', [
            'patient' => $patient
        ]);
    }

    #[Route('/patient/show/{id}/medicalFile', name: 'patient_show_medical_file')]
    public function command(int $id, PatientRepository $patientRepository, Request $request): Response
    {
        $this->manager->clear();
        $patient = $patientRepository->find($id);
        $medicalFile = $patient->getMedicalFile();
        $form = $this->createForm(MedicalFileType::class, $medicalFile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($medicalFile);
            $this->manager->flush();
        }

        return $this->renderForm('patient/includes/_medical_file.html.twig', [
            'patient' => $patient,
            'form' => $form
        ]);
    }

    #[Route('/patient/show/{id}/calendrier', name: 'patient_show_calendrier')]
    public function pieces(int $id, PatientRepository $patientRepository): Response
    {
        $patient = $patientRepository->find($id);

        return $this->renderForm('patient/includes/_calendrier.html.twig', [
            'patient' => $patient
        ]);
    }

    #[Route('/patient/show/{id}/invoice', name: 'patient_show_factures')]
    public function invoices(int $id, PatientRepository $patientRepository): Response
    {
        $patient = $patientRepository->find($id);

        return $this->renderForm('patient/includes/invoice/_invoices.html.twig', [
            'patient' => $patient
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/patient/medicalFileLine/delete/{id}', name: 'medical_file_line_delete')]
    public function deleteMedicalFileLines(MedicalFileLine $medicalFileLine, MedicalFileLineRepository $medicalFileLineRepository): Response
    {
        $medicalFileLineRepository->remove($medicalFileLine);
        return $this->json("Ok");
    }

    #[Route('/medicalFile/{id}/export/pdf', name: 'medical_file_export_pdf')]
    public function exportPdf(MedicalFile $medicalFile, Pdf $pdf, SluggerInterface $slugger): PdfResponse
    {
        $html    = $this->renderView('patient/includes/_pdf.html.twig', ['medicalFile' => $medicalFile]);
        $content = $pdf->getOutputFromHtml($html);
        $fileName = $slugger->slug("Dossier médical de ". $medicalFile->getPatient()->getLastName() . " " . $medicalFile->getPatient()->getFirstName()).'.pdf';

        return new PdfResponse($content, $fileName);
    }

    #[Route('/patient/{id}/upload/avatar', name: 'patient_upload_avatar')]
    public function uploadAvatar(Request $request, Patient $patient, SluggerInterface $slugger, Filesystem $filesystem): Response
    {
        $form = $this->createForm(AvatarType::class, $patient, [
            'action' => $request->getUri()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('file')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $directory = $this->getParameter('patient_avatar_directory').'/'.$patient->getId();
                    $filesystem->remove($directory);
                    $brochureFile->move(
                        $directory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e);
                }

                $patient->setAvatar($newFilename);

                $this->manager->persist($patient);
                $this->manager->flush();
            }
        }

        return $this->renderForm('_form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/patient/{id}/remove/avatar', name: 'remove_avatar_patient')]
    public function removeAvatar(Patient $patient, Filesystem $filesystem): Response
    {
        $directory = $this->getParameter('patient_avatar_directory').'/'.$patient->getId();
        $filesystem->remove($directory);

        $patient->setAvatar(null);

        $this->manager->persist($patient);
        $this->manager->flush();

        return $this->redirectToReferer();
    }

}
