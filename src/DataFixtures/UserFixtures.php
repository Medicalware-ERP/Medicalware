<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\EnumEntity;
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
        $profession = new UserType("Admin", "admin");

        $user
            ->setLastName('admin')
            ->setFirstName('admin')
            ->setPhoneNumber('0712124578')
            ->setProfession($profession)
            ->setAddress($address)
            ->setBirthdayDate(new DateTimeImmutable())
            ->setEmail('admin@medicalware.fr')
            ->setIsActive(true)
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'admin'))
        ;

        $manager->persist($profession);
        $manager->persist($user);
        $manager->flush();
    }
}
