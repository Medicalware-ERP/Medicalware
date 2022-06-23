<?php

namespace App\Controller;

use App\Entity\Planning\Event;
use App\Entity\Planning\Participant;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class EventController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/event/add', name: 'event_add')]
    public function add(Request $request): Response
    {
        $event = new Event();
        $class = $request->query->get("class");
        $id = $request->query->get("id");
        $allDay = $request->query->get("allDay") == "true";
        $startAt = $request->query->get("startAt");
        $endAt = $request->query->get("endAt");
        $event->setResourceClass($class);
        $event->setResourceId($id);
        $event->setAllDay($allDay);
        $event->setStartAt(new \DateTime($startAt));

        if ($endAt != null )
            $endAt = (new \DateTime($endAt))->modify("-1 day");
        else
            $endAt = (new \DateTime());

        $event->setEndAt($endAt);

        $form = $this->createForm(EventType::class, $event, [ "action" => $request->getUri() ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($event);
            $this->manager->flush();

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return $this->renderForm("event/_form.html.twig", [
            "form" => $form
        ]);
    }

    #[Route('/event/edit/time', name: 'event_edit_time')]
    public function editTime(Request $request): Response
    {
        $id = $request->query->get("id");
        $startAt = new \DateTime($request->query->get("startAt"));
        $endAt = new \DateTime($request->query->get("endAt"));

        $event = $this->manager->getRepository(Event::class)->find($id);

        $event->setStartAt($startAt);
        $event->setEndAt($endAt);

        $this->manager->persist($event);
        $this->manager->flush();

        return $this->json("Succes");
    }

    // Retourne les évènements lié à un type de ressource (Ex: Les events des Rooms)
    #[Route('/event/resource/{class}', name: 'event_resource_class')]
    public function getEventsResourceClass(Request $request, string $class): Response
    {
        $data = $this->manager->getRepository(Event::class)->findBy(["resourceClass" => $class]) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data, context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }

    // Retourne les évènements lié à une ressource bien précise (Ex: Les events de la Room ayant pour id: 13)
    #[Route('/event/resource/{class}/{id}', name: 'event_resource_id')]
    public function getEventsResourceId(Request $request, string $class, int $id): Response
    {
        $data = $this->manager->getRepository(Event::class)->findBy(["resourceClass" => $class, "resourceId" => $id]) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data, context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }

    // Retourne un évènements précis, suivant son id
    #[Route('/event/{id}', name: 'event_id')]
    public function getEvent(Request $request, int $id): Response
    {
        $data = $this->manager->find(Event::class, $id) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data, context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }
}
