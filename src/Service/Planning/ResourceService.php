<?php

namespace App\Service\Planning;

use App\Entity\EntityInterface;
use App\Entity\Planning\Resource;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

class ResourceService
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function getOrCreateResource(int $id, string $class): Resource
    {
        $resource = $this->manager->getRepository(Resource::class)->findOneBy(["resourceClass" => $class, "resourceId" => $id]);

        if ($resource == null) {
            $resource = new Resource();
            $resource->setResourceId($id);
            $resource->setResourceClass(ClassUtils::getRealClass($class));
        }

        return $resource;
    }
}