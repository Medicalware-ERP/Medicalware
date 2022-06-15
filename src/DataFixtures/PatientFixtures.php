<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\MedicalFile;
use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PatientFixtures extends Fixture
{
    /**
     * @throws \Exception
     */
    public function randomNumberOfSecuritySocial(): string
    {
        $randomNumberGender = random_int(1, 2);
        $randomNumberBirthDayDate = random_int(0, 9) . random_int(0, 9);
        $randomNumberMonth = random_int(0, 9) . random_int(0, 9);
        $randomNumberDepartment = random_int(0, 9) . random_int(0, 9);
        $randomNumberINSEE = random_int(0, 9) . random_int(0, 9) . random_int(0, 9);
        $randomNumberOrder = random_int(0, 9) . random_int(0, 9) . random_int(0, 9);
        $randomControlKey = random_int(0, 9) . random_int(0, 9);

        return $randomNumberGender . " " . $randomNumberBirthDayDate . " " . $randomNumberMonth . " " . $randomNumberDepartment . " " . $randomNumberINSEE . " " . $randomNumberOrder . " " . $randomControlKey;
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for($i = 0; $i <= 20 ; $i++){
            $patient = new Patient();
            $medicalFile = new MedicalFile();
            $address    = new Address($faker->streetName, $faker->city, $faker->postcode);
            $patient->setFirstName($faker->firstName);
            $patient->setLastName(($faker->lastName));
            $patient->setBirthdayDate($faker->dateTime);
            $patient->setGender("M");
            $patient->setEmail($faker->email);
            $numberTrunced = substr($faker->e164PhoneNumber,5, strlen($faker->e164PhoneNumber));
            $patient->setPhoneNumber("06". $numberTrunced);
            $patient->setNumberSocialSecurity($this->randomNumberOfSecuritySocial());
            $patient->setAddress($address);
            $patient->setMedicalFile($medicalFile);
            $manager->persist($patient);
        }

        $manager->flush();
    }
}
