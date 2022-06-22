<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Entity\Service;
use App\Form\ProviderType;
use App\Repository\EnumEntityRepository;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use App\Service\Provider\ProviderDataFormatter;
use App\Service\Service\ServiceDataFormatter;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends BaseController
{
    #[Route('/service', name: 'service_index')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig');
    }

    /**
     * @param Request $request
     * @param ServiceDataFormatter $serviceDataFormatter
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/serviceJson', name: 'service_json')]
    public function paginate(Request $request, ServiceDataFormatter $serviceDataFormatter): JsonResponse
    {
        return $this->paginateRequest(Service::class, $request, $serviceDataFormatter);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/service/delete/{id}', name: 'service_delete')]
    public function deleteService( Service $service, ServiceRepository $serviceRepository): Response
    {
        $serviceRepository->remove($service);
        return $this->redirectToRoute("service_index");
    }
}
