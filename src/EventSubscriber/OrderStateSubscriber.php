<?php

namespace App\EventSubscriber;


use App\Entity\Accounting\Order;
use App\Entity\Accounting\OrderState;
use App\Entity\Stock\StockHistory;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Workflow\InvoiceStateWorkflow;
use App\Workflow\OrderStateWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class OrderStateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface        $entityManager,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly MailerInterface $mailer,
        private readonly Environment $environment,
        private readonly Security $security,

    )
    {
    }

    public function handleTransition(TransitionEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof Order) {
            return;
        }

        $state = $this->entityManager->getRepository(OrderState::class)->findOneBy([
            'slug' => $event->getTransition()->getTos()[0]
        ]);

        if (!$state instanceof OrderState) {
            throw new RuntimeException();
        }

        $order->setState($state);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function handleValidation(GuardEvent $guardEvent)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_ACCOUNTANT')) {
            $guardEvent->setBlocked(true, 'Vous ne pouvais pas valider cette facture');
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function handleTransitionToValidate(TransitionEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof Order) {
            return;
        }

        $emails = $this->entityManager->getRepository(User::class)->findEmailsByProfession(UserTypeEnum::ACCOUNTANT);

        $html = '';

        try {
            $html = $this->environment->render('order/email/order_to_validate.html.twig', [
                'order' => $order
            ]);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
        }

        if (count($emails) > 0) {
            $email = (new Email())
                ->from('admin@medicalware.com')
                ->to(...$emails)
                ->subject('Validation commande')
                ->html($html)
            ;

            $this->mailer->send($email);
        }


    }

    public function handleTransitionDelivered(TransitionEvent $event)
    {
        /** @var Order $order */
        $order = $event->getSubject();

        if (!$order instanceof Order) {
            return;
        }

        $order->setDeliveryDate(new \DateTime());

        foreach ($order->getOrderLines() as $orderLine) {
            $stock = $orderLine->getEquipment()->getStock();
            $stock->addStockHistory(new StockHistory("Ajout de ". $orderLine->getQuantity(). " pièces", $this->security->getUser()));
            $this->entityManager->persist($stock);
            $orderLine->getEquipment()->getStock()->addQuantity($orderLine->getQuantity());
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.' . OrderStateWorkflow::NAME . '.transition' => 'handleTransition',
            'workflow.' . OrderStateWorkflow::NAME . '.transition.to_validate' => 'handleTransitionToValidate',
            'workflow.' . OrderStateWorkflow::NAME . '.transition.delivered' => 'handleTransitionDelivered',
            'workflow.' . OrderStateWorkflow::NAME . '.guard.validate' => 'handleValidation',
            'workflow.' . OrderStateWorkflow::NAME . '.guard.reject' => 'handleValidation',
        ];
    }
}
