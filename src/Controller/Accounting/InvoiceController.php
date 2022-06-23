<?php

namespace App\Controller\Accounting;

use App\Controller\BaseController;
use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceLine;
use App\Entity\Accounting\InvoiceState;
use App\Enum\Accounting\InvoiceStateEnum;
use App\Form\Accounting\InvoiceType;
use App\Repository\Accounting\InvoiceLineRepository;
use App\Repository\Accounting\InvoiceRepository;
use App\Service\Invoice\InvoiceDataFormatter;
use App\Workflow\InvoiceStateWorkflow;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

class InvoiceController extends BaseController
{
    #[Route('/invoice', name: 'invoice_index')]
    public function index(): Response
    {
        return $this->render('invoice/index.html.twig');
    }

    #[Route('/invoice/add', name: 'invoice_add')]
    public function add(Request $request, InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice();

        $state = $entityManager->getRepository(InvoiceState::class)->findOneBy([
            'slug' => InvoiceStateEnum::DRAFT
        ]);

        $invoice->setState($state);
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $invoiceRepository->add($invoice);
            }catch (UniqueConstraintViolationException $exception) {
                $form->get('reference')->addError(new FormError("Cette référence est déjà utilisé"));
            }
        }

        return $this->render('invoice/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/invoice/edit/{id}', name: 'invoice_edit')]
    public function edit(Invoice $invoice, Request $request, InvoiceRepository $invoiceRepository): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $invoiceRepository->add($invoice);
            }catch (UniqueConstraintViolationException $exception) {
                $form->get('reference')->addError(new FormError("Cette référence est déjà utilisé"));
            }
        }

        return $this->render('invoice/form.html.twig', [
            'form'      => $form->createView(),
            'invoice'   => $invoice
        ]);
    }

    #[Route('/invoice/show/{id}', name: 'invoice_show')]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/invoice/paginate', name: 'invoice_json')]
    public function paginate(Request $request, InvoiceDataFormatter $dataFormatter): Response
    {
        return $this->paginateRequest(Invoice::class, $request, $dataFormatter);
    }

    #[Route('/invoice/{id}/workflow/{transition}', name: 'invoice_workflow_transition')]
    public function workflowTransition(Request $request, Invoice $invoice, string $transition, Registry $registry): RedirectResponse
    {
        $workflow = $registry->get($invoice, InvoiceStateWorkflow::NAME);

        try {
            $workflow->apply($invoice, $transition);
        }catch (Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors du changement de status');
        }

        return $this->redirectToRoute('invoice_show', ['id' => $invoice->getId()]);
    }

    #[Route('/invoice/{id}/delete', name: 'invoice_delete')]
    public function delete(Invoice $invoice, InvoiceRepository $invoiceRepository): RedirectResponse
    {
        $invoiceRepository->remove($invoice);

        return $this->redirectToRoute('invoice_index');
    }

    #[Route('/invoice/{id}/export/pdf', name: 'invoice_export_pdf')]
    public function exportPdf(Invoice $invoice, Pdf $pdf): PdfResponse
    {
        $html    = $this->renderView('invoice/pdf/_pdf.html.twig', ['invoice' => $invoice]);
        $content = $pdf->getOutputFromHtml($html);

        return new PdfResponse($content, 'facture_'.$invoice->getDate()->format('d_m_Y').".pdf");
    }

    #[Route('/invoice/delete/{id}/line', name: 'invoice_delete_line')]
    public function deleteLine(InvoiceLine $order, InvoiceLineRepository $repository): JsonResponse
    {
        $repository->remove($order);

        return $this->json('ok');

    }
}
