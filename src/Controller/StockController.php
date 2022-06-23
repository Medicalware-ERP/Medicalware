<?php

namespace App\Controller;

use App\Entity\Stock\Stock;
use App\Entity\Stock\StockHistory;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use App\Form\Stock\StockType;
use App\Repository\Stock\StockRepository;
use App\Service\Stock\StockDataFormatter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class StockController extends BaseController
{
    #[Route('/stock', name: 'stock_index')]
    public function index(): Response
    {
        return $this->render('stock/index.html.twig', [
            'controller_name' => 'StockController',
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/stock/paginate', name: 'stock_paginate')]
    public function paginate(Request $request, StockDataFormatter $dataFormatter): Response
    {
        return $this->paginateRequest(Stock::class, $request, $dataFormatter);
    }

    #[Route('/stock/add', name: 'stock_add')]
    public function add(Request $request, StockRepository $repository): Response
    {
        $stock  = new Stock();
        $form   = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $repository->add($stock);
            } catch (UniqueConstraintViolationException $exception) {
                $form->get('equipment')->get('reference')->addError(new FormError("Cette référence est déjà utilisé"));
            }
        }

        return $this->renderForm('stock/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/stock/{id}/edit', name: 'stock_edit')]
    public function edit(Request $request, Stock $stock, StockRepository $repository): Response
    {
        $form   = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $repository->add($stock);
            } catch (UniqueConstraintViolationException $exception) {
                $form->get('equipment')->get('reference')->addError(new FormError("Cette référence est déjà utilisé"));
            }
        }

        return $this->renderForm('stock/form.html.twig', [
            'form' => $form,
            'stock' => $stock
        ]);
    }

    #[Route('/stock/remove/{id}', name: 'stock_remove')]
    public function remove(Stock $stock, StockRepository $stockRepository): Response
    {
        $stock->setArchivedAt(new \DateTimeImmutable());

        $stockRepository->add($stock);
        return $this->redirectToRoute('stock_index');
    }

    #[Route('/stock/{id}/information', name: 'stock_show_information')]
    public function showInformation(Stock $stock): Response
    {
        return $this->render('stock/includes/_informations.html.twig', [
            'stock' => $stock
        ]);
    }

    #[Route('/stock/{id}/command', name: 'stock_show_command')]
    public function showCommand(Stock $stock): Response
    {
        return $this->render('stock/includes/_command.html.twig', [
            'stock' => $stock
        ]);
    }

    #[Route('/stock/{id}/history', name: 'stock_show_history')]
    public function showHistory(Stock $stock): Response
    {
        return $this->render('stock/includes/_history.html.twig', [
            'stock' => $stock
        ]);
    }

    #[Route('/stock/{id}/askorder', name: 'stock_ask_order')]
    public function askOrder(Stock $stock, Environment $environment, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {     $emails = $entityManager->getRepository(User::class)->findEmailsByProfession(UserTypeEnum::STOCK_MANAGER);

        try {
            $html = $environment->render('stock/email/_ask_order.html.twig', [
                'equipment' => $stock->getEquipment()
            ]);

            if (count($emails) > 0) {
                $email = (new Email())
                    ->from('admin@medicalware.com')
                    ->to(...$emails)
                    ->subject("Demande d'une commande")
                    ->html($html)
                ;
                $stock->addStockHistory(new StockHistory("Une demande de commande à étais envoyé", $this->getUser()));
                $entityManager->persist($stock);
                $entityManager->flush();
                $mailer->send($email);
            }
        } catch (\Exception|TransportExceptionInterface) {

        }

        return $this->json('ok');
    }
}
