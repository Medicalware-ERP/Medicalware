<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\Provider;
use App\Entity\Service;
use App\Form\ProviderType;
use App\Repository\EnumEntityRepository;
use App\Repository\ProviderRepository;
use App\Repository\ServiceRepository;
use App\Service\Provider\ProviderDataFormatter;
use App\Service\Service\ServiceDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN_SERVICE")]
class ServiceController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

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
        $modifier = function(QueryBuilder $queryBuilder){
            $queryBuilder->andWhere("e.isArchived = 0");
        };

        return $this->paginateRequest(Service::class, $request, $serviceDataFormatter, $modifier);
    }

    #[Route('/service/toArchive/{id}', name: 'app_to_archive_service')]
    public function archive(int $id): Response
    {
        $service = $this->manager->find(Service::class, $id) ?? throw new NotFoundHttpException("Patient non trouvé");

        if ($service == null){
            throw new NotFoundHttpException();
        }

        $service->setIsArchived(true);
        $service->setIsArchivedAt(new \DateTimeImmutable());

        $this->manager->persist($service);
        $this->manager->flush();
        return $this->redirectToRoute("service_index");
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
