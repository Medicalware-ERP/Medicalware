<?php

namespace App\DataFixtures;

use App\Entity\Room\Room;
use App\Entity\Room\RoomOption;
use App\Entity\Room\RoomType;
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

    $manager->persist($repos);
    $manager->persist($arcade);
    $manager->persist($office);

    $tv = new RoomOption("télévision", "Avec télé");
    $projo = new RoomOption("projecteur", "Avec projo");
    $console = new RoomOption("console de jeu", "Avec console");

    $manager->persist($tv);
    $manager->persist($projo);
    $manager->persist($console);

    $room1 = new Room();

    $room1
        ->setLabel("Salle de repos #624")
        ->setCapacity(1)
        ->setType($repos)
    ;

    $room1->addOption($tv);

    $manager->persist($room1);

    $room2 = new Room();

    $room2
        ->setLabel("Salle d'arcade du 2ème")
        ->setCapacity(1)
        ->setType($arcade);
    ;

    $room2->addOption($console);
    $room2->addOption($tv);

    $manager->persist($room2);

    $room3 = new Room();

    $room3
        ->setLabel("Bureaux de la compta")
        ->setCapacity(1)
        ->setType($office);
    ;

    $room3->addOption($projo);

    $manager->persist($room3);

    $manager->flush();
}
}
