<?php

namespace App\Controller;


use App\Form\EnumType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class EnumController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/addEnum/{class}', name: 'add_enum')]
    public function add(Request $request, string $class): Response
    {
        $data = new $class();
        $form = $this->createForm(EnumType::class, $data, [ "action" => $request->getUri() ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->manager->persist($data);
            $this->manager->flush();

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return $this->renderForm("enum/_form.html.twig", [
            "form" => $form
        ]);
    }
}
