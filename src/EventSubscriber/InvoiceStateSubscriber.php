<?php

namespace App\EventSubscriber;

use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceState;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Workflow\InvoiceStateWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Knp\Snappy\Pdf;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class InvoiceStateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface        $entityManager,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly MailerInterface $mailer,
        private readonly Environment $environment,
        private readonly Pdf $pdf
    )
    {
    }

    public function handleTransition(TransitionEvent $event)
    {
        $invoice = $event->getSubject();

        if (!$invoice instanceof Invoice) {
            return;
        }

        $state = $this->entityManager->getRepository(InvoiceState::class)->findOneBy([
            'slug' => $event->getTransition()->getTos()[0]
        ]);

        if (!$state instanceof InvoiceState) {
            throw new RuntimeException();
        }

        $invoice->setState($state);
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
        $invoice = $event->getSubject();

        if (!$invoice instanceof Invoice) {
            return;
        }

        $emails = $this->entityManager->getRepository(User::class)->findEmailsByProfession(UserTypeEnum::ACCOUNTANT);

        $html = '';

        try {
            $html = $this->environment->render('invoice/email/invoice_to_validate.html.twig', [
                'invoice' => $invoice
            ]);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
        }

        if (count($emails) > 0) {
            $email = (new Email())
                ->from('admin@medicalware.com')
                ->to(...$emails)
                ->subject('Validation facture')
                ->html($html)
            ;

            $this->mailer->send($email);
        }

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function handleTransitionValidate(TransitionEvent $event)
    {
        $invoice = $event->getSubject();

        if (!$invoice instanceof Invoice) {
            return;
        }

        $emails = $invoice?->getPatient()->getEmail() ?? [];

        $html = '';
        $pdfHtml = "";

        try {
            $html = $this->environment->render('invoice/email/invoice_patient_to_pay.html.twig', [
                'invoice' => $invoice
            ]);
            $pdfHtml =  $this->environment->render('invoice/pdf/_pdf.html.twig', [
                'invoice' => $invoice,
                'pdfEmail' => true
            ]);

        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            dd($e);
        }

        $pdfGenerated = $this->pdf->getOutputFromHtml($pdfHtml);


        if (count($emails) > 0) {
            $email = (new Email())
                ->from('admin@medicalware.com')
                ->to(...$emails)
                ->subject('Vous avez une nouvelle ')
                ->html($html)
                ->attach($pdfGenerated, sprintf('facture-%s.pdf', date('Y-m-d')))
            ;

            $this->mailer->send($email);
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.' . InvoiceStateWorkflow::NAME . '.transition' => 'handleTransition',
            'workflow.' . InvoiceStateWorkflow::NAME . '.transition.to_validate' => 'handleTransitionToValidate',
            'workflow.' . InvoiceStateWorkflow::NAME . '.transition.validate' => 'handleTransitionValidate',
            'workflow.' . InvoiceStateWorkflow::NAME . '.guard.validate' => 'handleValidation',
            'workflow.' . InvoiceStateWorkflow::NAME . '.guard.reject' => 'handleValidation',
        ];
    }
}
