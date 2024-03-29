<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Planning\Resource;
use App\Entity\Provider;
use App\Form\ProviderType;
use App\Repository\ProviderRepository;
use App\Service\Provider\ProviderDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[IsGranted("ROLE_ADMIN_STOCK")]
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
    public function add(Request $request, ProviderRepository $providerRepository, EntityManagerInterface $entityManager): Response
    {
        $provider = new Provider();

        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $providerRepository->add($provider);
            $resource = new Resource();
            $resource->setResourceId($provider->getId());
            $resource->setResourceClass(Provider::class);

            $entityManager->persist($resource);
            $entityManager->flush();
            return $this->redirectToReferer();
        }

        return $this->renderForm('provider/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/provider/edit/{id}', name: 'provider_edit')]
    public function edit(Request $request, int $id, ProviderRepository $providerRepository): Response
    {
        $provider = $providerRepository->find($id);

        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $providerRepository->edit($provider);
            return $this->redirectToReferer();
        }

        return $this->renderForm('provider/form.html.twig', [
            'form' => $form,
            'provider' => $provider
        ]);
    }

    #[Route('/provider/remove/{id}', name: 'provider_remove')]
    public function remove(Provider $provider, ProviderRepository $providerRepository): Response
    {
        $provider->setArchivedAt(new \DateTimeImmutable());
        $providerRepository->add($provider);

        return $this->redirectToRoute('provider_index');
    }

    #[Route('/provider/show/{id}/information', name: 'provider_show_information')]
    public function info(int $id, ProviderRepository $providerRepository): Response
    {
        $provider = $providerRepository->find($id);

        return $this->renderForm('provider/includes/_informations.html.twig', [
            'provider' => $provider
        ]);
    }

    #[Route('/provider/show/{id}/command', name: 'provider_show_command')]
    public function command(int $id, ProviderRepository $providerRepository): Response
    {
        $provider = $providerRepository->find($id);

        return $this->renderForm('provider/includes/_command.html.twig', [
            'provider' => $provider
        ]);
    }

    #[Route('/provider/show/{id}/pieces', name: 'provider_show_piece')]
    public function pieces(int $id, ProviderRepository $providerRepository): Response
    {
        $provider = $providerRepository->find($id);

        return $this->renderForm('provider/includes/_piece.html.twig', [
            'provider' => $provider
        ]);
    }

    #[Route('/provider/show/{id}/planning', name: 'provider_show_planning')]
    public function providerPlanning(int $id, ProviderRepository $providerRepository): Response
    {
        $provider = $providerRepository->find($id);

        return $this->renderForm('provider/includes/_show_planning.html.twig', [
            'provider' => $provider
        ]);
    }
}
