<?php

namespace App\DataFixtures;

use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceLine;
use App\Entity\Accounting\Order;
use App\Entity\Accounting\OrderLine;
use App\Entity\Patient;
use App\Entity\Provider;
use App\Entity\Stock\Equipment;
use App\Entity\Tva;
use App\Enum\Accounting\InvoiceStateEnum;
use App\Enum\Accounting\OrderStateEnum;
use App\Enum\Accounting\PaymentMethodEnum;
use App\Enum\TvaEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $equipments = $manager->getRepository(Equipment::class)->findAll();
        $providers = $manager->getRepository(Provider::class)->findAll();
        $tvas = $manager->getRepository(Tva::class)->findAll();

        $states = (new OrderStateEnum())->getData();
        foreach ($states as $state) {
            $manager->persist($state);
        }


        $paymentMethods =  (new PaymentMethodEnum())->getData();

        foreach ($paymentMethods as $pm) {
            $manager->persist($pm);
        }

        for ($i = 0; $i <= 20; $i++) {
            $invoice = new Order();
            $state = $states[rand(0, 4)];
            $tva = $tvas[rand(0, 3)];
            $paymentMethod = $paymentMethods[rand(0, 2)];
            $provider  = $providers[rand(0, 19)];

            for ($j = 0; $j <= rand(1, 10); $j++) {
                $orderLine = new OrderLine();
                $equipment = $equipments[rand(0, 19)];
                $orderLine
                    ->setEquipment($equipment)
                    ->setDescription($faker->paragraph(1))
                    ->setPrice($faker->numberBetween(1, 999))
                    ->setQuantity($faker->numberBetween(1, 10))
                    ->calculateHt()
                ;
                $invoice->addOrderLine($orderLine);
            }

            $date = $faker->dateTimeBetween('-1 years');
            $invoice
                ->setDeliveryPlannedDate($date)
                ->setState($state)
                ->setTva($tva)
                ->setPaymentMethod($paymentMethod)
                ->setProvider($provider)
                ->calculate()
            ;

            if ($state->getSlug() === OrderStateEnum::DELIVERY) {
                $invoice->setDeliveryDate($date->modify('+20 days'));
            }

            $manager->persist($invoice);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            InvoiceFixtures::class,
            ProviderFixtures::class,
            StockFixtures::class
        ];
    }
}
