<?php

namespace App\DataFixtures;

use App\Enum\ServiceEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $service = new ServiceEnum();
        $states = $service->getData();
        foreach($states as $state){
            $manager->persist($state);
        }

        $manager->flush();
    }
}
