<?php

namespace App\EventSubscriber;

use App\Entity\Stock\Stock;
use App\Entity\Stock\StockHistory;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class StockSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
        private readonly Environment $environment,
        private readonly Security $security,
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $stock = $args->getObject();

        if (!$stock instanceof Stock) {
            return;
        }

        $stock->addStockHistory(new StockHistory("Le stock à étais modifier", $this->security->getUser()));

        $args->getObjectManager()->persist($stock);
        $args->getObjectManager()->flush();

        $this->execute($args);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->execute($args);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function execute(LifecycleEventArgs $args)
    {
        $stock = $args->getObject();

        if (!$stock instanceof Stock) {
            return;
        }

        if ($stock->getQuantity() === 0) {
            $emails = $this->entityManager->getRepository(User::class)->findEmailsByProfession(UserTypeEnum::STOCK_MANAGER);

            $html = '';

            try {
                $html = $this->environment->render('stock/email/_no_quantity.html.twig', [
                    'equipment' => $stock->getEquipment()
                ]);
            } catch (LoaderError|RuntimeError|SyntaxError $e) {
            }

            if (count($emails) > 0) {
                $email = (new Email())
                    ->from('admin@medicalware.com')
                    ->to(...$emails)
                    ->subject('Alerte stock')
                    ->html($html)
                ;

                $stock->addStockHistory(new StockHistory("Vous n'avez plus de stock", $this->security->getUser()));

                $args->getObjectManager()->persist($stock);
                $args->getObjectManager()->flush();

                $this->mailer->send($email);
            }
        }
    }
}
