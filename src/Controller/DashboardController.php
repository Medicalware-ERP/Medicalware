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
        $countUser = $userRepository->count([]);
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
       DoctorRepository $doctorRepository,
       UserRepository $userRepository,
       PatientRepository $patientRepository
    ): JsonResponse
   {
       $countDoctor = $doctorRepository->count([]);
       $countUser = $userRepository->count([]);
       $countPatient = $patientRepository->count([]);

       return $this->json([
           'Docteurs' => $countDoctor,
           'Utilisateurs' => $countUser,
           'Patients' => $countPatient
       ]);
   }
}
