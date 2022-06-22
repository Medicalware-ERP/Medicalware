<?php

namespace App\Controller;

use App\Entity\Planning\Event;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    // Retourne les évènements lié à un type de ressource (Ex: Les events des Rooms)
    #[Route('/event/resource/{class}', name: 'event_resource_class')]
    public function getEventsResourceClass(Request $request, string $class): Response
    {
        $data = $this->manager->getRepository(Event::class)->findBy(["resourceClass" => $class]) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data);
    }

    // Retourne les évènements lié à une ressource bien précise (Ex: Les events de la Room ayant pour id: 13)
    #[Route('/event/resource/{class}/{id}', name: 'event_resource_id')]
    public function getEventsResourceId(Request $request, string $class, int $id): Response
    {
        $data = $this->manager->getRepository(Event::class)->findBy(["resourceClass" => $class, "resourceId" => $id]) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data);
    }

    // Retourne un évènements précis, suivant son id
    #[Route('/event/{id}', name: 'event_id')]
    public function getEvent(Request $request, int $id): Response
    {
        $data = $this->manager->find(Event::class, $id) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data);
    }
}
