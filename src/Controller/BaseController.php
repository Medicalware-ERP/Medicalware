<?php

namespace App\Controller;

use App\Entity\EntityInterface;
use App\Service\DataFormatterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    public const LIMIT = 2;

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EntityManagerInterface::class => EntityManagerInterface::class,
            ],
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function paginateRequest(string $entity, Request $request, DataFormatterInterface $formatter): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $query = $request->query->get('query');
        $limit = $request->query->get('limit', self::LIMIT);

        $manager = $this->container->get(EntityManagerInterface::class);
        $datas = $manager->getRepository($entity)->paginate($page, $limit, $query);

        $data = [];

        foreach ($datas as $user) {
            $data[] = $formatter->format($user);
        }

        return $this->json([
            'data' => $data,
            'filteredCount' => $datas->count(),
            'totalCount' => $manager->getRepository($entity)->count([])
        ]);
    }

}
