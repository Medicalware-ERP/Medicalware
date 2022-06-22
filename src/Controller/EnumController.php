<?php

namespace App\Controller;


use App\Entity\EnumEntity;
use App\Entity\MedicalFileLine;
use App\Entity\Service;
use App\Form\EnumType;
use App\Repository\EnumEntityRepository;
use App\Repository\MedicalFileLineRepository;
use App\Repository\ServiceRepository;
use App\Service\Service\ServiceDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EnumController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/addEnum/{class}', name: 'add_enum')]
    public function add(Request $request, string $class, SluggerInterface $slugger): Response
    {
        $data = new $class();
        $form = $this->createForm(EnumType::class, $data, [ "action" => $request->getUri() ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data->setSlug($slugger->slug($data->getName()));
            $this->manager->persist($data);
            $this->manager->flush();

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return $this->renderForm("enum/_form.html.twig", [
            "form" => $form
        ]);
    }

    #[Route('/editEnum/{class}/{id}', name: 'edit_enum')]
    public function edit(Request $request, string $class, int $id): Response
    {
        $data = $this->manager->find($class, $id) ?? throw new NotFoundHttpException("Entité non trouvée");
        $form = $this->createForm(EnumType::class, $data, [ "action" => $request->getUri() ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($data);
            $this->manager->flush();

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return $this->renderForm("enum/_form.html.twig", [
            "form" => $form,
            "data" => $data
        ]);
    }
}
