<?php

namespace App\Controller;

use App\Entity\Room\Room;
use App\Repository\RoomRepository;
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

    #[Route('/room/delete/{id}', name: 'app_delete_room')]
    public function delete(int $id, RoomRepository $roomRepository)
    {
        $room = $roomRepository->find($id);

        if ($room == null)
            throw new NotFoundHttpException();

        $roomRepository->remove($room);

        return $this->redirectToRoute("app_room");
    }

    #[Route('/room/{id}', name: 'app_show_room')]
    public function show(int $id): Response
    {
        $room = $this->manager->find(Room::class, $id) ?? throw new NotFoundHttpException("Salle non trouvée");


        return $this->render('room/show.html.twig', [
            'room' => $room
        ]);
    }
}
