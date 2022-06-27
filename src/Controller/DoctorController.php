<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Form\DoctorType;
use App\Service\Doctor\DoctorDataFormatter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class DoctorController extends BaseController
{
    use ResetPasswordControllerTrait;

   public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly MailerInterface $mailer
    )
    {
    }

    #[Route('/doctors', name: 'app_doctor')]
    public function index(): Response
    {
        return $this->render('doctor/index.html.twig');
    }

    /**
     * @param Request $request
     * @param DoctorDataFormatter $doctorDataFormatter
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/doctorsJson', name: 'doctors_json')]
    public function paginate(Request $request, DoctorDataFormatter $doctorDataFormatter): JsonResponse
    {
        return $this->paginateRequest(Doctor::class, $request, $doctorDataFormatter);
    }

    #[Route('/doctor/add', name: 'app_add_doctor')]
    public function addDoctor(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $doctor = new Doctor();

        $form = $this->createForm(DoctorType::class, $doctor);
        $form->handleRequest($request);

        $profession = $this->manager->getRepository(\App\Entity\UserType::class)->findOneBy([
            'slug' => UserTypeEnum::DOCTOR
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctor->setPassword($userPasswordHasher->hashPassword($doctor, uniqid()));
            $doctor->setService($doctor->getSpecialisation()->getService());
            $doctor->setProfession($profession);
            $doctor->setRoles(["ROLE_DOCTOR"]);

            try {
                $this->manager->persist($doctor);
                $this->manager->flush();
                $this->processSendingPasswordResetEmail($doctor);

            } catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError("Cet email est déjà utilisé"));
                return $this->renderForm('doctor/form.html.twig', [
                    'form' => $form
                ]);
            }

            return $this->redirectToRoute("app_doctor");
        }

        return $this->renderForm('doctor/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/doctor/edit/{id}', name: 'app_edit_doctor')]
    public function edit(Request $request, int $id): Response
    {
        $doctor = $this->manager->find(Doctor::class, $id) ?? throw new NotFoundHttpException("Docteur non trouvé");

        $form = $this->createForm(DoctorType::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processSendingPasswordResetEmail($doctor);

            try {
                $this->manager->persist($doctor);
                $this->manager->flush();
            } catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError("Cette email est déjà utilisé"));
                return $this->renderForm('doctor/form.html.twig', [
                    'form' => $form
                ]);
            }

            return $this->redirectToRoute("app_doctor");
        }

        return $this->renderForm('doctor/form.html.twig', [
            'form' => $form,
            'doctor' => $doctor
        ]);
    }

    #[Route('/doctor/{id}', name: 'app_show_doctor')]
    public function show(int $id): Response
    {
        $doctor = $this->manager->find(Doctor::class, $id) ?? throw new NotFoundHttpException("Docteur non trouvé");

        return $this->render('doctor/show.html.twig', [
            'doctor' => $doctor
        ]);
    }

    public function processSendingPasswordResetEmail(User $user): void
    {

        if (!$user->isActive() || $user->getActivatedAt() instanceof \DateTimeInterface) {
            return;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $exception) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la création du token');
            return;
        }

        $email = (new TemplatedEmail())
            ->from(new Address('admin@medicalware.com', 'Medicalware'))
            ->to($user->getEmail())
            ->subject('Veuillez créer votre mot de passe')
            ->htmlTemplate('reset_password/email_create_password.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface) {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoie du mail de la création du mot de passe');
            return;
        }

        $user->setActivatedAt(new \DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);
    }

}
