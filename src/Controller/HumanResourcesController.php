<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Form\DoctorType;
use App\Form\UserType;
use App\Service\User\UserDataFormatter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HumanResourcesController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
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

        if ($form->isSubmitted() && $form->isValid()) {
            $doctor->setPassword($userPasswordHasher->hashPassword($doctor, 'admin'));
            $doctor->setProfession((new UserTypeEnum())->getData()[3]);
            $doctor->setService($doctor->getSpecialisation()->getService());
            $doctor->setRoles(["ROLE_DOCTOR"]);

            try {
                $this->manager->persist($doctor);
                $this->manager->flush();

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
            'user' => $user
        ]);
    }
}
