<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Form\ProviderType;
use App\Service\Provider\ProviderDataFormatter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProviderController extends BaseController
{
    #[Route('/provider', name: 'provider_index')]
    public function index(): Response
    {
        return $this->render('provider/index.html.twig');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/provider/json', name: 'provider_json')]
    public function paginate(Request $request, ProviderDataFormatter $dataFormatter): Response
    {
        return $this->paginateRequest(Provider::class, $request, $dataFormatter);
    }

    #[Route('/provider/add', name: 'provider_add')]
    public function add(Request $request): Response
    {
        $provider = new Provider();

        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($provider);
        }

        return $this->renderForm('provider/form.html.twig', [
            'form' => $form
        ]);
    }

}
