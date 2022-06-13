<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Provider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProviderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for($i = 0; $i <= 20 ; $i++){
            $provider = new Provider();
            $address = new Address($faker->streetName, $faker->city, $faker->postcode);
            $provider->setName($faker->company);
            $provider->setEmail($faker->companyEmail);
            $numberFormatted = substr($faker->e164PhoneNumber, -strlen($faker->e164PhoneNumber) + 5);
            $provider->setPhone("06". $numberFormatted);
            $provider->setAddress($address);
            $manager->persist($provider);
        }


        $manager->flush();
    }
}
