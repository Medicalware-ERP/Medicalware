<?php

namespace App\Controller;

use App\Entity\Planning\Resource;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Form\AvatarType;
use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use App\Service\User\UserDataFormatter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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

    #[IsGranted('ROLE_HUMAN_RESOURCE')]
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
        $modifier = function(QueryBuilder $queryBuilder){
            $queryBuilder->join('e.profession', 'u')
                        ->where('u.slug != :slug')
                        ->setParameter('slug', UserTypeEnum::DOCTOR);
        };

        return $this->paginateRequest(User::class, $request, $userDataFormatter,$modifier);
    }

    #[IsGranted('ROLE_HUMAN_RESOURCE')]
    #[Route('/user/add', name: 'app_add_user')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, uniqid()));

            try {
                $this->manager->persist($user);
                $this->manager->flush();

                $resource = new Resource();
                $resource->setResourceId($user->getId());
                $resource->setResourceClass(User::class);

                $this->manager->persist($resource);
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



    #[Route('/user/edit/{id}', name: 'app_edit_user')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvé");
        $this->denyAccessUnlessGranted('USER_VIEW_EDIT', $user);
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

            return $this->redirectToReferer();
        }

        return $this->renderForm('human_resources/form.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }

    #[IsGranted('ROLE_HUMAN_RESOURCE')]
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

    #[Route('/user/include/show/{id}', name: 'app_show_user')]
    public function showUser(int $id) : Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvée");
        $this->denyAccessUnlessGranted('USER_VIEW_EDIT', $user);

        return $this->render('human_resources/includes/_show_user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/include/show/{id}/planning', name: 'app_show_user_planning')]
    public function showUserPlanning(int $id) : Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvée");
        $this->denyAccessUnlessGranted('USER_VIEW_EDIT', $user);

        return $this->render('human_resources/includes/_show_planning.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/upload/avatar', name: 'upload_avatar')]
    public function uploadAvatar(Request $request, User $user, SluggerInterface $slugger, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted('USER_VIEW_EDIT', $user);
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
        $this->denyAccessUnlessGranted('USER_VIEW_EDIT', $user);
        $directory = $this->getParameter('user_avatar_directory').'/'.$user->getId();
        $filesystem->remove($directory);

        $user->setAvatar(null);

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->redirectToReferer();
    }

    #[IsGranted('ROLE_HUMAN_RESOURCE')]
    #[Route('/user/{id}/delete', name: 'user_delete')]
    public function delete(User $user, Filesystem $filesystem): Response
    {
        $directory = $this->getParameter('user_avatar_directory').'/'.$user->getId();
        $filesystem->remove($directory);

        $user->setLeftAt(new \DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->json("ok");
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

    #[Route('/user/{id}/change/password', name: 'user_change_password')]
    public function changePassword(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('USER_VIEW_EDIT', $user);
        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'action' => $request->getUri()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $this->manager->persist($user);
            $this->manager->flush();
        }

        return $this->renderForm('_form.html.twig', [
            'form' => $form
        ]);
    }

}
