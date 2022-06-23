<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Planning\Participant;

class ParticipantSubscriber
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {

    }

    public function postLoad(Participant $participant)
    {
        $resource = $this->manager->getRepository($participant->getResourceClass())->find($participant->getResourceId());

        $participant->setResource($resource);
    }
}
