<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PatientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for($i = 0; $i <= 20 ; $i++){
            $patient = new Patient();
            $address    = new Address($faker->streetName, $faker->city, $faker->postcode);
            $patient->setFirstName($faker->firstName);
            $patient->setLastName(($faker->lastName));
            $patient->setBirthdayDate($faker->dateTime);
            $patient->setEmail($faker->email);
            $numberTrunced = substr($faker->e164PhoneNumber,5, strlen($faker->e164PhoneNumber));
            $patient->setPhoneNumber("06". $numberTrunced);
            $patient->setNumberSocialSecurity("2 95 04 39 02 123 203 33");
            $patient->setAddress($address);
            $manager->persist($patient);
        }

        $manager->flush();
    }
}
