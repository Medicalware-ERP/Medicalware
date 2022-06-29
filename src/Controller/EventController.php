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
use function PHPUnit\Framework\throwException;

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

    // Retourne les évènements lié à une ressource bien précise (Ex: Les events de la Room ayant pour id: 13)
    #[Route('/event/resource/{class}/{id}', name: 'event_resource_id')]
    public function getEventsResourceId(Request $request, string $class, int $id): Response
    {
        $resource = $this->manager->getRepository(Resource::class)->findBy(["resourceClass" => $class, "resourceId" => $id]) ?? throw new NotFoundHttpException("Ressource non trouvée");
        $data = $this->manager->getRepository(Event::class)->findEventsBy($resource[0]) ?? throw new NotFoundHttpException("Entité non trouvée");

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
        $resources = $this->manager->getRepository(Resource::class)->findAllActive();
        $events = $this->manager->getRepository(Event::class)->findAllFromResourcesAndAttendees();

        return $this->json(["resources" => $resources, "events" => $events ], context: [AbstractNormalizer::GROUPS => [ "main" ] ]);
    }

    #[Route('/event/period/validity', name: 'event_period_validity')]
    public function checkEventValididty(Event $event)
    {
        dd($event);
        $eventRepository = $this->manager->getRepository(Event::class);
        /* TODO : Vérification sur la période de l'event que :
            - La ressource n'a pas un autre event
            -Les attendees n'ont pas d'autre event
        */

        /* TODO : Récupération de TOUT les EVENTS => on attrape tous ceux qui sont lié en ressource ou en attendees
            à la ressource et aux attendees de CET event et on check si les périodes ce chevauge, si oui on récup l'évent */

        // Vu qu'on récup tous les events de TOUS LE MONDE, les attendees sont afficher en mode ressource
        // Faire une recherche sur les events between ?
        $events = $this->manager->getRepository(Event::class)->findAllFromResourcesAndAttendees();
        $resource = $event->getResource()->getResource();
        $attendees = $event->getAttendees();

        // On commence d'abord par vérifier les ressources

        // Puis les attendees
        /** @var Participant $attendee */
        foreach ($attendees as $attendee)
        {
            $startAt = $attendee->getResource()->getStartAt();
            $endAt = $attendee->getResource()->getEndAt();

            // Ca va marcher que pour les User du coup car patient pas pris en compte dans requete
            $events = $eventRepository->findEventsInPeriodByUser($attendee->getId(), $startAt, $endAt);

        }

        return true;
    }
}
