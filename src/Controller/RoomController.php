<?php

namespace App\Controller;

use App\Entity\Room\Room;
use App\Form\RoomType;
use App\Repository\RoomOptionRepository;
use App\Repository\RoomRepository;
use App\Repository\RoomTypeRepository;
use App\Service\Room\RoomDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->paginateRequest(Room::class, $request, $roomDataFormatter);
    }

    #[Route('/room/add', name: 'app_add_room')]
    public function add(Request $request): Response
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($room);
            $this->manager->flush();

            return $this->redirectToRoute("index_room");
        }

        return $this->renderForm('room/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/room/edit/{id}', name: 'app_edit_room')]
    public function edit(Request $request, int $id): Response
    {
        $room = $this->manager->find(Room::class, $id) ?? throw new NotFoundHttpException("Salle non trouvé");

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($room);
            $this->manager->flush();

            return $this->redirectToRoute("index_room");
        }

        return $this->renderForm('room/form.html.twig', [
            'form' => $form,
            'room' => $room
        ]);
    }

    #[Route('/room/delete/{id}', name: 'app_delete_room')]
    public function delete(int $id, RoomRepository $roomRepository)
    {
        $room = $roomRepository->find($id);

        if ($room == null)
            throw new NotFoundHttpException();

        $roomRepository->remove($room);

        return $this->redirectToRoute("index_room");
    }

    #[Route('/room/{id}', name: 'app_show_room')]
    public function show(int $id): Response
    {
        $room = $this->manager->find(Room::class, $id) ?? throw new NotFoundHttpException("Salle non trouvée");

        return $this->render('room/show.html.twig', [
            'room' => $room
        ]);
    }

    #[Route('/room/include/list', name: 'index_room')]
    public function roomIndex(RoomRepository $roomRepository) : Response
    {
        $provider = $roomRepository->findAll();

        return $this->renderForm('room/includes/_room.html.twig', [
            'rooms' => $provider
        ]);
    }

    #[Route('/room/include/type', name: 'index_room_type')]
    public function roomTypeIndex(RoomTypeRepository $roomTypeRepository) : Response
    {
        $provider = $roomTypeRepository->findAll();

        return $this->renderForm('room/includes/_types.html.twig', [
            'roomTypes' => $provider
        ]);
    }

    #[Route('/room/include/option', name: 'index_room_option')]
    public function roomOptionIndex(RoomOptionRepository $roomOptionRepository) : Response
    {
        $provider = $roomOptionRepository->findAll();

        return $this->renderForm('room/includes/_options.html.twig', [
            'roomOptions' => $provider
        ]);
    }
}
