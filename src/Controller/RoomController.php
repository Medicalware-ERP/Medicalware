<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\User\UserDataFormatter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends BaseController
{
    #[Route('/room', name: 'app_room')]
    public function index(): Response
    {
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @param Request $request
     * @param UserDataFormatter $userDataFormatter
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/roomsJson', name: 'rooms_json')]
    public function paginate(Request $request, UserDataFormatter $userDataFormatter): JsonResponse
    {
        return $this->paginateRequest(User::class, $request, $userDataFormatter);
    }
}
