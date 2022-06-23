<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Doctor;
use App\Entity\MedicalFile;
use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
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
        $doctors = $manager->getRepository(Doctor::class)->findAll();
        for($i = 0; $i <= 20 ; $i++){
            $patient = new Patient();
            $medicalFile = new MedicalFile();
            $numberTrunced = substr($faker->e164PhoneNumber,5, strlen($faker->e164PhoneNumber));
            $address    = new Address($faker->streetName, $faker->city, $faker->postcode);
            $patient->setFirstName($faker->firstName)
                    ->setLastName(($faker->lastName))
                    ->setBirthdayDate($faker->dateTime)
                    ->setGender("M")
                    ->setBloodGroup($faker->bloodGroup())
                    ->setEmail($faker->email)
                    ->setPhoneNumber("06". $numberTrunced)
                    ->setNumberSocialSecurity($this->randomNumberOfSecuritySocial())
                    ->setAddress($address)
                    ->setDoctor($doctors[$i])
                    ->setMedicalFile($medicalFile);
            $manager->persist($patient);
        }

        $manager->flush();
    }
}
