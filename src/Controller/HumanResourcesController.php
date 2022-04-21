<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\User\UserDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HumanResourcesController extends AbstractController
{
    public const LIMIT = 2;

    public function __construct(private EntityManagerInterface $manager)
    {
    }

    #[Route('/humanResources', name: 'app_human_resources')]
    public function index(): Response
    {
        $limit = self::LIMIT;
        $count = $this->manager->getRepository(User::class)->getTotalUsers();
        return $this->render('human_resources/index.html.twig', [
            'limit' => $limit,
            'total' => $count
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/usersJson', name: 'users_json')]
    public function getUsersJSON(Request $request, UserDataFormatter $userManager): JsonResponse
    {
        $page = $request->query->get("page") ?? 1;
        $query = $request->query->get("query") ?? null;
        $users = $this->manager->getRepository(User::class)->getAllUsersPaginated($page, self::LIMIT, $query);
        $data = [];

        foreach ($users as $user) {
            $data[] = $userManager->format($user);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }


    #[Route('/user/{id}', name: 'app_show_user')]
    public function show(int $id): Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvée");

        return $this->render('human_resources/show.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_edit_user')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvée");

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($user);
            $this->manager->flush();
        }

        return $this->renderForm('human_resources/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/user/disable/{id}', name: 'app_toggle_active_user')]
    public function toggleActiveUser(Request $request, int $id): RedirectResponse
    {
        $user = $this->manager->find(User::class, $id) ?? throw new NotFoundHttpException("Utilisateur non trouvée");

        $user->setIsActive(!$user->isActive());

        $this->manager->persist($user);
        $this->manager->flush();


        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }
}
