<?php

namespace App\Controller;

use App\Entity\Planning\Resource;
use App\Entity\Room\Room;
use App\Form\RoomType;
use App\Repository\RoomOptionRepository;
use App\Repository\RoomRepository;
use App\Repository\RoomTypeRepository;
use App\Service\Room\RoomDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN_SERVICE")]
class RoomController extends BaseController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route('/room', name: 'app_room')]
    public function index(): Response
    {
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @param Request $request
     * @param RoomDataFormatter $roomDataFormatter
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/roomsJson', name: 'rooms_json')]
    public function paginate(Request $request, RoomDataFormatter $roomDataFormatter): JsonResponse
    {
        $modifier = function(QueryBuilder $queryBuilder){
            $queryBuilder->andWhere("e.archivedAt is null");
        };

        return $this->paginateRequest(Room::class, $request, $roomDataFormatter, $modifier);
    }

    #[Route('/room/add', name: 'app_add_room')]
    public function add(Request $request): Response
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room, [ "action" => $request->getUri() ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($room);
            $this->manager->flush();

            $resource = new Resource();
            $resource->setResourceId($room->getId());
            $resource->setResourceClass(Room::class);

            $this->manager->persist($resource);
            $this->manager->flush();

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return $this->renderForm('room/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/room/edit/{id}', name: 'app_edit_room')]
    public function edit(Request $request, int $id): Response
    {
        $room = $this->manager->find(Room::class, $id) ?? throw new NotFoundHttpException("Salle non trouvé");

        $form = $this->createForm(RoomType::class, $room, [ "action" => $request->getUri() ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($room);
            $this->manager->flush();

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        return $this->renderForm('room/form.html.twig', [
            'form' => $form,
            'room' => $room
        ]);
    }

    #[Route('/room/archive/{id}', name: 'app_archive_room')]
    public function archive(int $id, RoomRepository $roomRepository)
    {
        $room = $roomRepository->find($id);

        if ($room == null)
            throw new NotFoundHttpException();

        $room->setArchivedAt(new \DateTimeImmutable());
        $roomRepository->add($room);

        return $this->redirectToRoute("index_room");
    }

    #[Route('/room/{id}/show', name: 'show_room_information')]
    public function showInformation(int $id) : Response
    {
        $room = $this->manager->find(Room::class, $id) ?? throw new NotFoundHttpException("Salle non trouvée");

        return $this->renderForm('room/includes/_show_room.html.twig', [
            'room' => $room
        ]);
    }

    #[Route('/room/{id}/planning', name: 'show_room_planning')]
    public function showPlanning(int $id) : Response
    {
        $room = $this->manager->find(Room::class, $id) ?? throw new NotFoundHttpException("Salle non trouvée");

        return $this->renderForm('room/includes/_show_planning.html.twig', [
            'room' => $room
        ]);
    }

    #[Route('/room/include/list', name: 'index_room')]
    public function roomIndex(RoomRepository $roomRepository) : Response
    {
        $data = $roomRepository->findAllActive();

        return $this->renderForm('room/includes/_room.html.twig', [
            'rooms' => $data
        ]);
    }

    #[Route('/room/include/type', name: 'index_room_type')]
    public function roomTypeIndex(RoomTypeRepository $roomTypeRepository) : Response
    {
        $data = $roomTypeRepository->findAllActive();

        return $this->renderForm('room/includes/_types.html.twig', [
            'roomTypes' => $data
        ]);
    }

    #[Route('/room/type/archive/{id}', name: 'app_archive_room_type')]
    public function archiveType(int $id, RoomTypeRepository $roomTypeRepository)
    {
        $type = $roomTypeRepository->find($id);

        if ($type == null)
            throw new NotFoundHttpException();

        $type->setArchivedAt(new \DateTimeImmutable());
        $roomTypeRepository->add($type);

        return $this->redirectToRoute("index_room_type");
    }

    #[Route('/room/include/option', name: 'index_room_option')]
    public function roomOptionIndex(RoomOptionRepository $roomOptionRepository) : Response
    {
        $data = $roomOptionRepository->findAllActive();

        return $this->renderForm('room/includes/_options.html.twig', [
            'roomOptions' => $data
        ]);
    }

    #[Route('/room/option/archive/{id}', name: 'app_archive_room_option')]
    public function archiveOption(int $id, RoomOptionRepository $roomOptionRepository)
    {
        $option = $roomOptionRepository->find($id);

        if ($option == null)
            throw new NotFoundHttpException();

        $option->setArchivedAt(new \DateTimeImmutable());
        $roomOptionRepository->add($option);

        return $this->redirectToRoute("index_room_option");
    }
}
