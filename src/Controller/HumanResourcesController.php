<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Form\AvatarType;
use App\Form\DoctorType;
use App\Form\UserType;
use App\Service\User\UserDataFormatter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class HumanResourcesController extends BaseController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface       $manager,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly EntityManagerInterface       $entityManager,
        private readonly MailerInterface              $mailer
    )
    {
    }

    #[Route('/humanResources', name: 'app_human_resources')]
    public function index(): Response
    {
        return $this->render('human_resources/index.html.twig');
    }

    /**
     * @param Request $request
     * @param UserDataFormatter $userDataFormatter
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/usersJson', name: 'users_json')]
    public function paginate(Request $request, UserDataFormatter $userDataFormatter): JsonResponse
    {
        return $this->paginateRequest(User::class, $request, $userDataFormatter);
    }

    #[Route('/user/add', name: 'app_add_user')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, 'admin'));

            try {
                $this->manager->persist($user);
                $this->manager->flush();

                $this->processSendingPasswordResetEmail($user);
            } catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError("Cette email est déjà utilisé"));
                return $this->renderForm('human_resources/form.html.twig', [
                    'form' => $form
                ]);
            }

            return $this->redirectToRoute("app_human_resources");
        }

        return $this->renderForm('human_resources/form.html.twig', [
            'form' => $form
        ]);
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
            $doctor->setPassword($userPasswordHasher->hashPassword($doctor, 'admin'));
            $doctor->setProfession($profession);
            $doctor->setRoles(["ROLE_DOCTOR"]);


            try {
                $this->manager->persist($doctor);
                $this->manager->flush();
                $this->processSendingPasswordResetEmail($doctor);

            } catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError("Cet email est déjà utilisé"));
                return $this->renderForm('human_resources/doctor/form.html.twig', [
                    'form' => $form
                ]);
            }

            return $this->redirectToRoute("app_human_resources");
        }

        return $this->renderForm('human_resources/doctor/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_edit_user')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvé");

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processSendingPasswordResetEmail($user);

            try {
                $this->manager->persist($user);
                $this->manager->flush();
            } catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError("Cette email est déjà utilisé"));
                return $this->renderForm('human_resources/form.html.twig', [
                    'form' => $form
                ]);
            }

            return $this->redirectToRoute("app_human_resources");
        }

        return $this->renderForm('human_resources/form.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }

    #[Route('/user/disable/{id}', name: 'app_toggle_active_user')]
    public function toggleActiveUser(Request $request, int $id): Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvée");

        $user->setIsActive(!$user->isActive());

        $this->processSendingPasswordResetEmail($user);

        $this->manager->persist($user);
        $this->manager->flush();

        $referer = $request->headers->get('referer');

        return $request->isXmlHttpRequest() ? $this->json('ok') : $this->redirect($referer);
    }

    #[Route('/user/{id}', name: 'app_show_user')]
    public function show(int $id): Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvée");

        return $this->render('human_resources/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/upload/avatar', name: 'upload_avatar')]
    public function uploadAvatar(Request $request, User $user, SluggerInterface $slugger, Filesystem $filesystem): Response
    {
        $form = $this->createForm(AvatarType::class, $user, [
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
                    $directory = $this->getParameter('user_avatar_directory').'/'.$user->getId();
                    $filesystem->remove($directory);
                    $brochureFile->move(
                        $directory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e);
                }

                $user->setAvatar($newFilename);

                $this->manager->persist($user);
                $this->manager->flush();
            }
        }

        return $this->renderForm('_form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/user/{id}/remove/avatar', name: 'remove_avatar')]
    public function removeAvatar(User $user, Filesystem $filesystem): Response
    {
        $directory = $this->getParameter('user_avatar_directory').'/'.$user->getId();
        $filesystem->remove($directory);

        $user->setAvatar(null);

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->redirectToReferer();
    }

    private function processSendingPasswordResetEmail(User $user): void
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
            ]);

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
