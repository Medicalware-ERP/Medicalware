<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Room;
use App\Entity\RoomType;
use App\Entity\User;
use App\Entity\UserType;
use App\Enum\RoomTypeEnum;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixtures extends Fixture
{
    public function __construct()
    {
    }

/**
 * @throws \Exception
 */
public function load(ObjectManager $manager): void
{
    $repos = new RoomType("repos", "salle de repos");
    $arcade = new RoomType("arcade", "salle d'arcade");
    $office = new RoomType("office", "bureaux");

    $room1 = new Room();

    $room1
        ->setLabel("Salle de repos #624")
        ->setCapacity(1)
        ->setType($repos);
    ;

    $manager->persist($room1);
    $manager->persist($repos);

    $room2 = new Room();

    $room2
        ->setLabel("Salle d'arcade du 2ème")
        ->setCapacity(1)
        ->setType($arcade);
    ;

    $manager->persist($room2);
    $manager->persist($arcade);

    $room3 = new Room();

    $room3
        ->setLabel("Bureaux de la ocmpta")
        ->setCapacity(1)
        ->setType($office);
    ;

    $manager->persist($room3);
    $manager->persist($office);

    $manager->flush();
}
}
