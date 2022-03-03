<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HumanResourcesController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    #[Route('/humanResources', name: 'app_human_resources')]
    public function index(): Response
    {
        return $this->render('human_resources/index.html.twig', [
            'users' => $this->manager->getRepository(User::class)->findAll()
        ]);
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
}
