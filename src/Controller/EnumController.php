<?php

namespace App\Controller;


use App\Form\EnumType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnumController extends AbstractController
{

    #[Route('/addEnum/{class}', name: 'add_enum')]
    public function add(Request $request, string $class): Response
    {
        $data = new $class();
        $form = $this->createForm(EnumType::class, $data);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            dd($data);
        }

        return $this->renderForm("enum/_form.html.twig", [
            "form" => $form
        ]);
    }
}
