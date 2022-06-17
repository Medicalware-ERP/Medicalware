<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Doctor;
use App\Entity\UserType;
use App\Enum\SpecialisationEnum;
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
        $states = $specialisation->getData();
        foreach($states as $state){
            $manager->persist($state);
        }

        for($i = 0; $i <= 20 ; $i++){
            $doctor = new Doctor();
            $specialisation = $states[rand(1, 17)];
            $profession = new UserType("Docteur", "docteur");
            $address    = new Address($faker->streetName , $faker->city, $faker->postcode);
            $numberTrunced = substr($faker->e164PhoneNumber,5, strlen($faker->e164PhoneNumber));
            $doctor
                ->addSpecialisation($specialisation)
                ->setLastName($faker->lastName)
                ->setFirstName($faker->firstName)
                ->setPhoneNumber("06". $numberTrunced)
                ->setProfession($profession)
                ->setAddress($address)
                ->setBirthdayDate(new DateTimeImmutable())
                ->setEmail($faker->email)
                ->setIsActive(true)
                ->setGender("M")
                ->setRoles(["ROLE_ADMIN"])
                ->setPassword($this->userPasswordHasher->hashPassword($doctor, 'admin'));
            $manager->persist($profession);
            $manager->persist($doctor);
        }
        $manager->flush();
    }
}
