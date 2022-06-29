<?php

namespace App\Controller;

use App\Repository\Planning\EventTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/planning/index', name: 'index_planning')]
    public function indexPlanning(EventTypeRepository $eventTypeRepository) : Response
    {
        return $this->renderForm('planning/index.html.twig');
    }

    #[Route('/planning/event/type', name: 'index_event_type')]
    public function indexEventType(EventTypeRepository $eventTypeRepository) : Response
    {
        $data = $eventTypeRepository->findAllActive();

        return $this->renderForm('planning/types.html.twig', [
            'eventTypes' => $data
        ]);
    }

    #[Route('/planning/event/type/archive/{id}', name: 'archive_event_type')]
    public function archiveEventType(int $id, EventTypeRepository $eventTypeRepository)
    {
        $type = $eventTypeRepository->find($id);

        if ($type == null)
            throw new NotFoundHttpException();

        $type->setArchivedAt(new \DateTimeImmutable());
        $eventTypeRepository->add($type);

        return $this->redirectToRoute("index_event_type");
    }
}
