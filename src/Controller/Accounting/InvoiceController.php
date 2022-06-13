<?php

namespace App\Controller\Accounting;

use App\Controller\BaseController;
use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceState;
use App\Enum\Accounting\InvoiceStateEnum;
use App\Form\Accounting\InvoiceType;
use App\Repository\Accounting\InvoiceRepository;
use App\Service\Invoice\InvoiceDataFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $invoiceRepository->add($invoice);
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
            $invoiceRepository->add($invoice);
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
            'invoice' => $invoice
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/invoice/paginate', name: 'invoice_json')]
    public function paginate(Request $request, InvoiceDataFormatter $dataFormatter ): Response
    {
        return $this->paginateRequest(Invoice::class, $request, $dataFormatter);
    }
}
