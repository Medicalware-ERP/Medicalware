<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Accounting\InvoiceRepository;
use App\Repository\Accounting\OrderRepository;
use App\Repository\DoctorRepository;
use App\Repository\PatientRepository;
use App\Repository\Planning\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        DoctorRepository $doctorRepository,
        UserRepository $userRepository,
        PatientRepository $patientRepository,
        InvoiceRepository $invoiceRepository,
        OrderRepository $orderRepository,
        EventRepository $eventRepository
    ): Response
    {
        $countDoctor = $doctorRepository->count([]);
        $countUser = $userRepository->countAllUsers();
        $countPatient = $patientRepository->count([]);

        $invoicesPayed = $invoiceRepository->findByDelivery();
        $invoicesPrices = [];
        foreach ($invoicesPayed as $invoice) {
            $invoicesPrices[] = $invoice->getTtc();
        }
        $totalInvoicesPrices = array_sum($invoicesPrices);

        $orderPayed = $orderRepository->findByDelivery();
        $orderPrices = [];
        foreach ($orderPayed as $order) {
            $orderPrices[] = $order->getTtc();
        }
        $totalOrderPrices = array_sum($orderPrices);
        $user = $this->getUser()->getId();
        $eventsOfUserOfToday = $eventRepository->findEventOfTodayByUser($user);
        return $this->render('dashboard/index.html.twig', [
            'countDoctor' => $countDoctor,
            'countUser' => $countUser,
            'countPatient' => $countPatient,
            'invoicesPayed' => $invoicesPayed,
            'orderPayed' => $orderPayed,
            'totalOrderPrices' => $totalOrderPrices,
            'totalInvoicesPrices' => $totalInvoicesPrices,
            'eventsOfUserOfToday' => $eventsOfUserOfToday,
        ]);
    }


    #[Route('/stats', name: 'stats')]
   public function statsUsers(
        InvoiceRepository $invoiceRepository,
        OrderRepository $orderRepository,
    ): JsonResponse
   {
       $invoicesValided = $invoiceRepository->findByValid();
       $invoicesPrices = [];
       foreach ($invoicesValided as $invoice) {
           $invoicesPrices[] = $invoice->getTtc();
       }
       $totalInvoicesPricesValided = array_sum($invoicesPrices);
       $ordersValided = $orderRepository->findByValid();
       $orderPrices = [];
       foreach ($ordersValided as $order) {
           $orderPrices[] = $order->getTtc();
       }
       $totalOrderPricesValided = array_sum($orderPrices);

       $invoicesPayed = $invoiceRepository->findByDelivery();
       $invoicesPrices = [];
       foreach ($invoicesPayed as $invoice) {
           $invoicesPrices[] = $invoice->getTtc();
       }
       $totalInvoicesPricesPayed = array_sum($invoicesPrices);
       $ordersPayed = $orderRepository->findByDelivery();
       $orderPrices = [];
       foreach ($ordersPayed as $order) {
           $orderPrices[] = $order->getTtc();
       }
       $totalOrderPricesPayed = array_sum($orderPrices);

       return $this->json([
           'Total crédit en attente' => $totalInvoicesPricesValided,
           'Total débit en attente' => $totalOrderPricesValided,
           'Total débité' => $totalOrderPricesPayed,
           'Total crédité' => $totalInvoicesPricesPayed,
       ]);
   }
}
