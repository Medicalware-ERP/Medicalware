<?php

namespace App\DataFixtures;

use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceLine;
use App\Entity\Patient;
use App\Enum\Accounting\InvoiceStateEnum;
use App\Enum\Accounting\PaymentMethodEnum;
use App\Enum\TvaEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        
        $states = (new InvoiceStateEnum())->getData();
        foreach ($states as $state) {
            $manager->persist($state);
        }

        $tvas = (new TvaEnum())->getData();
        foreach ($tvas as $t) {
            $manager->persist($t);
        }

        $paymentMethods =  (new PaymentMethodEnum())->getData();

        foreach ($paymentMethods as $pm) {
            $manager->persist($pm);
        }

        for ($i = 0; $i <= 20; $i++) {
            $invoice = new Invoice();
            $state = $states[rand(0, 4)];
            $tva = $tvas[rand(0, 3)];
            $paymentMethod = $paymentMethods[rand(0, 2)];

            for ($j = 0; $j <= rand(1, 10); $j++) {
                $invoiceLine = new InvoiceLine();

                $invoiceLine
                    ->setReference($faker->paragraph(1))
                    ->setDescription($faker->paragraph(1))
                    ->setPrice($faker->numberBetween(1, 999))
                    ->setQuantity($faker->numberBetween(1, 10))
                    ->calculateHt()
                ;
                $invoice->addInvoiceLine($invoiceLine);
            }


            $invoice
                ->setReference($faker->text(10))
                ->setDate($faker->dateTimeBetween('-1 years'))
                ->setState($state)
                ->setTva($tva)
                ->setPatient($manager->getRepository(Patient::class)->findAll()[rand(0, 20)])
                ->setPaymentMethod($paymentMethod)
                ->calculate()
            ;

            $manager->persist($invoice);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
          PatientFixtures::class
        ];
    }
}
