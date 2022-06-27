<?php

namespace App\EventSubscriber;

use App\Entity\Planning\Resource;
use Doctrine\ORM\EntityManagerInterface;

class ResourceSubscriber
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {

    }

    public function postLoad(Resource $resource)
    {
        $entity = $this->manager->getRepository($resource->getResourceClass())->find($resource->getResourceId());

        $resource->setResource($entity);
    }
}
