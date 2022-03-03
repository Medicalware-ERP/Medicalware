<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
