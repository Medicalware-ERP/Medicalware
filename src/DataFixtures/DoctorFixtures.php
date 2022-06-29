<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Doctor;
use App\Entity\Service;
use App\Entity\Specialisation;
use App\Entity\UserType;
use App\Enum\ServiceEnum;
use App\Enum\SpecialisationEnum;
use App\Enum\UserTypeEnum;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DoctorFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create("fr_FR");

        $specialisation = new SpecialisationEnum();
        $service = new ServiceEnum();
        $services = $service->getData();

        /** @var Service $state */
        foreach($services as $service){
            $manager->persist($service);
        }

        $specialisations = $specialisation->getData();
        /** @var Specialisation $state */
        foreach($specialisations as $key => $state){
            $state->setService($services[$key]);
            $manager->persist($state);
        }

        $profession = (new UserTypeEnum())->getData()[3];
        $manager->persist($profession);

        for($i = 0; $i <= 16 ; $i++){
            $doctor = new Doctor();
            $address    = new Address($faker->streetName , $faker->city, $faker->postcode);
            $numberTrunced = substr($faker->e164PhoneNumber,5, strlen($faker->e164PhoneNumber));
            $doctor
                ->setService($services[$i])
                ->setSpecialisation($specialisations[$i])
                ->setLastName($faker->lastName)
                ->setFirstName($faker->firstName)
                ->setPhoneNumber("06". $numberTrunced)
                ->setProfession($profession)
                ->setAddress($address)
                ->setBirthdayDate(new DateTimeImmutable())
                ->setEmail($faker->email)
                ->setIsActive(false)
                ->setGender("H")
                ->setRoles(["ROLE_DOCTOR"])
                ->setPassword($this->userPasswordHasher->hashPassword($doctor, 'admin'));

            $manager->persist($doctor);
        }
        $manager->flush();
    }
}
