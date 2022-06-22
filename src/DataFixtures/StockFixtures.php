<?php

namespace App\DataFixtures;

use App\Entity\Provider;
use App\Entity\Service;
use App\Entity\Stock\Equipment;
use App\Entity\Stock\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class StockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create("fr_FR");
        $providers = $manager->getRepository(Provider::class)->findAll();
        $services = $manager->getRepository(Service::class)->findAll();

        for($i = 0; $i <= 20 ; $i++) {
            $stock = new Stock();
            $equipment = new Equipment();
            $provider  = $providers[rand(0, 19)];

            $equipment
                ->setReference($faker->name)
                ->setName($faker->name)
                ->setPrice(rand(3, 1000))
                ->setProvider($provider)
                ->addService($services[rand(0, 16)])
            ;
            $stock->setEquipment($equipment);
            $stock->setQuantity(rand(0, 100));

            $manager->persist($stock);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ServiceFixtures::class,
            ProviderFixtures::class
        ];
    }
}
