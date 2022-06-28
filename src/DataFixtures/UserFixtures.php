<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use App\Entity\UserType;
use App\Enum\UserTypeEnum;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user       = new User();
        $address    = new Address("50 rue de la rue", "Marseille", "13015");
        $professions = (new UserTypeEnum())->getData();
        $length     = count($professions) - 1;
        $profession = $professions[rand(0, $length)];

        $user
            ->setLastName('Boulakhlas')
            ->setFirstName('Kais')
            ->setPhoneNumber('0712124578')
            ->setProfession($profession)
            ->setAddress($address)
            ->setBirthdayDate(new DateTimeImmutable())
            ->setEmail('admin@medicalware.fr')
            ->setIsActive(true)
            ->setGender("M")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'admin'))
        ;

        $manager->persist($profession);
        $manager->persist($user);
        $manager->flush();

        $user1       = new User();
        $address1    = new Address("50 rue de la rue", "Marseisdlle", "13015");
        $profession1 = new UserType("Médecin", "medecin");

        $user1
            ->setLastName('Quetglas')
            ->setFirstName('Loris')
            ->setPhoneNumber('0713124578')
            ->setProfession($profession1)
            ->setAddress($address1)
            ->setGender("M")
            ->setBirthdayDate(new DateTimeImmutable())
            ->setEmail('admin@medicfdfalware.fr')
            ->setIsActive(true)
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->userPasswordHasher->hashPassword($user1, 'admin1'))
        ;

        $manager->persist($profession1);
        $manager->persist($user1);
        $manager->flush();

        $user2       = new User();
        $address2    = new Address("50 rue de la rue", "Marssaaeille", "13015");
        $profession2 = new UserType("Professeur", "professeur");

        $user2
            ->setLastName('Waskar')
            ->setFirstName('Said')
            ->setPhoneNumber('0712454578')
            ->setProfession($profession2)
            ->setAddress($address2)
            ->setGender("M")
            ->setBirthdayDate(new DateTimeImmutable())
            ->setEmail('admin@medicalwafsdfsdre.fr')
            ->setIsActive(true)
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->userPasswordHasher->hashPassword($user2, 'admin'))
        ;

        $manager->persist($profession2);
        $manager->persist($user2);
        $manager->flush();
    }
}
