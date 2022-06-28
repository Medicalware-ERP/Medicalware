<?php

namespace App\Controller;

use App\Entity\Planning\Event;
use App\Entity\Planning\Participant;
use App\Entity\Planning\Resource;
use App\Form\EventType;
use App\Service\Planning\ResourceService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use function App\Service\Planning\getOrCreateResource;

class EventController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/event/add', name: 'event_add')]
    public function add(Request $request, ResourceService $resourceService): Response
    {
        $event = new Event();
        $id = $request->query->get("id");
        $class = $request->query->get("class");
        $allDay = $request->query->get("allDay") == "true";
        $startAt = $request->query->get("startAt");
        $endAt = $request->query->get("endAt");
        $event->setAllDay($allDay);
        $event->setStartAt(new \DateTime($startAt));

        $resource = $resourceService->getOrCreateResource($id, $class);
        $event->setResource($resource);

        if ($endAt != null)
        {
            $endAt = (new \DateTime($endAt));
            if ($allDay) $endAt = ($endAt)->modify("-1 day");
        }
        else
        {
            $endAt = (new \DateTime());
        }

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

    #[Route('/event/edit/{id}', name: 'event_edit')]
    public function edit(Request $request, int $id): Response
    {
        $event = $this->manager->getRepository(Event::class)->find($id);

        $form = $this->createForm(EventType::class, $event, [ "action" => $request->getUri() ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($event);
            $this->manager->flush();

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return $this->renderForm("event/_form.html.twig", [
            "form" => $form,
            "event" => $event
        ]);
    }

    #[Route('/event/edit/time/{id}', name: 'event_edit_time')]
    public function editTime(Request $request, int $id): Response
    {
        $startAt = new \DateTime($request->query->get("startAt"));
        $endAt = new \DateTime($request->query->get("endAt"));

        $event = $this->manager->getRepository(Event::class)->find($id);

        $event->setStartAt($startAt);
        $event->setEndAt($endAt);

        $this->manager->persist($event);
        $this->manager->flush();

        return $this->json("Succes");
    }

    #[Route('/event/edit/time/resource/{id}', name: 'event_edit_time_resource')]
    public function editTimeAndResource(Request $request, int $id): Response
    {
        $startAt = new \DateTime($request->query->get("startAt"));
        $endAt = new \DateTime($request->query->get("endAt"));
        $resourceId = $request->query->get("newResourceId");

        /** @var Event $event */
        $event = $this->manager->getRepository(Event::class)->find($id);
        $event->setStartAt($startAt);
        $event->setEndAt($endAt);

        if ($resourceId != null)
        {
            $resource = $this->manager->getRepository(Resource::class)->find($resourceId);
            $event->setResource($resource);
        }

        $this->manager->persist($event);
        $this->manager->flush();

        return $this->json("Succes");
    }

    #[Route('/event/show/{id}}', name: 'event_show')]
    public function showEvent(Request $request, int $id){
        $event = $this->manager->getRepository(Event::class)->find($id);

    if ($event == null) throw new NotFoundHttpException();

        return $this->renderForm('event/_show.html.twig', [
            'event' => $event
        ]);
    }

    // Retourne les évènements lié à un type de ressource (Ex: Les events des Rooms)
    #[Route('/event/resource/{class}', name: 'event_resource_class')]
    public function getEventsResourceClass(Request $request, string $class): Response
    {
        $resource = $this->manager->getRepository(Resource::class)->findBy(["resourceClass" => $class]) ?? throw new NotFoundHttpException("Ressources non trouvées");
        $data = $this->manager->getRepository(Event::class)->findBy(["resource" => $resource]) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data, context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }

    // Retourne les évènements lié à une ressource bien précise (Ex: Les events de la Room ayant pour id: 13)
    #[Route('/event/resource/{class}/{id}', name: 'event_resource_id')]
    public function getEventsResourceId(Request $request, string $class, int $id): Response
    {
        $resource = $this->manager->getRepository(Resource::class)->findBy(["resourceClass" => $class, "resourceId" => $id]) ?? throw new NotFoundHttpException("Ressource non trouvée");
        $data = $this->manager->getRepository(Event::class)->findBy(["resource" => $resource]) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data, context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }

    // Retourne un évènements précis, suivant son id
    #[Route('/event/{id}', name: 'event_id')]
    public function getEvent(Request $request, int $id): Response
    {
        $data = $this->manager->find(Event::class, $id) ?? throw new NotFoundHttpException("Entité non trouvée");

        return $this->json($data, context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }

    #[Route('/event/delete/{id}', name: 'event_delete')]
    public function delete(Request $request, int $id)
    {
        /**@var \App\Repository\Planning\EventRepository $eventRepository*/
        $eventRepository = $this->manager->getRepository(Event::class);
        $event = $eventRepository->find($id);

        if ($event == null) throw new NotFoundHttpException();

        $eventRepository->remove($event);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    #[Route('/resource/all', name: 'event_resources')]
    public function getAllResources(Request $request): Response
    {
        $resources = $this->manager->getRepository(Resource::class)->findAll();
        $events = $this->manager->getRepository(Event::class)->findAll();

        return $this->json(["resources" => $resources, "events" => $events ], context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }
}
