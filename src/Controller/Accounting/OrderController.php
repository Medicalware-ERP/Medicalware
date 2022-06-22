<?php

namespace App\Controller\Accounting;

use App\Controller\BaseController;
use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\Order;
use App\Entity\Accounting\OrderLine;
use App\Entity\Accounting\OrderState;
use App\Enum\Accounting\OrderStateEnum;
use App\Form\Accounting\OrderType;
use App\Repository\Accounting\InvoiceRepository;
use App\Repository\Accounting\OrderLineRepository;
use App\Repository\Accounting\OrderRepository;
use App\Service\Order\OrderDataFormatter;
use App\Workflow\InvoiceStateWorkflow;
use App\Workflow\OrderStateWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

class OrderController extends BaseController
{
    #[Route('/order', name: 'order_index')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/order/paginate', name: 'order_paginate')]
    public function paginate(Request $request, OrderDataFormatter $dataFormatter): JsonResponse
    {
        return $this->paginateRequest(Order::class, $request, $dataFormatter);
    }

    #[Route('/order/add', name: 'order_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();

        $state = $entityManager->getRepository(OrderState::class)->findOneBy([
           'slug' => OrderStateEnum::DRAFT
        ]);
        $order->setState($state);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->renderForm('order/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/order/{id}/edit', name: 'order_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Order $order): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->renderForm('order/form.html.twig', [
            'form' => $form,
            'order' => $order
        ]);
    }

    #[Route('/order/{id}/show', name: 'order_show')]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/order/{id}/workflow/{transition}', name: 'order_workflow_transition')]
    public function workflowTransition(Order $order, string $transition, Registry $registry): RedirectResponse
    {
        $workflow = $registry->get($order, OrderStateWorkflow::NAME);

        try {
            $workflow->apply($order, $transition);
        }catch (Exception $exception) {
            $this->addFlash('error', 'Une erreur est survenue lors du changement de status');
        }

        return $this->redirectToRoute('order_show', ['id' => $order->getId()]);
    }

    #[Route('/order/{id}/delete', name: 'order_delete')]
    public function delete(Request $request, Order $order, OrderRepository $repository): RedirectResponse
    {
        $repository->remove($order);

        $referer = $request->headers->get('referer');

        return $this->redirect($referer);

    }

    #[Route('/order/{id}/export/pdf', name: 'order_export_pdf')]
    public function exportPdf(Order $order, Pdf $pdf): PdfResponse
    {
        $html    = $this->renderView('order/pdf/_pdf.html.twig', ['order' => $order]);
        $content = $pdf->getOutputFromHtml($html);

        return new PdfResponse($content, 'commande_'.(new \DateTime())->format('d_m_Y').".pdf");
    }

    #[Route('/order/delete/{id}/line', name: 'order_delete_line')]
    public function deleteLine(OrderLine $order, OrderLineRepository $repository): JsonResponse
    {
        $repository->remove($order);

        return $this->json('ok');

    }
}
