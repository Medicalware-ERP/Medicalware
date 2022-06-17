<?php

namespace App\DataFixtures;

use App\Enum\DiseaseEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DiseaseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $disease = new DiseaseEnum();
        $states = $disease->getData();
        foreach($states as $state){
            $manager->persist($state);
        }

        $manager->flush();
    }
}
