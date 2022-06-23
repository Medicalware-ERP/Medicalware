<?php

namespace App\Service\Room;

use App\Entity\EntityInterface;
use App\Entity\Room;
use App\Service\DataFormatterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RoomDataFormatter implements DataFormatterInterface
{
    public function __construct(private Environment $environment)
    {
    }

    /**
     * @param Room $data
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function format(EntityInterface $data): array
    {
        return [
            'label' => $this->environment->render('room/datatable/columns/label.html.twig', [
                'room' => $data
            ]),
            'capacity' => $this->environment->render('room/datatable/columns/capacity.html.twig', [
                'room' => $data
            ]),
            'type' => $this->environment->render('room/datatable/columns/type.html.twig', [
                'room' => $data
            ]),
            'actions' => $this->environment->render('room/datatable/columns/actions.html.twig', [
                'room' => $data
            ]),
        ];
    }
}