<?php

namespace App\Controller;

use App\Repository\ApartmentRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home(ApartmentRepository $apartmentRepository, ReservationRepository $reservationRepository, ReservationController $reservationController)
  {
    $number_of_apartments = $apartmentRepository->getTotalApartments();
    $number_of_reservations = $reservationRepository->getTotalReservations();
    $total_sales = $reservationRepository->getTotalReservationsSales();
    $apartments = $apartmentRepository->findAll();

    // Get all reservations data
    $reservations = $reservationRepository->findAll();
    $newReservations = [];

    // Format reservations data
    foreach ($reservations as $reservation) {
      $newReservations[] = [
        'id' => $reservation->getId(),
        'start' => $reservation->getStartAt()->format('Y-m-d'),
        'end' => $reservation->getEndAt()->format('Y-m-d'),
        'title' => $reservation->getClient()->getFullName(),
        'backgroundColor' => $reservation->getApartment()->getColor(),
        'textColor' => 'white',
        'url' => $reservationController->generateUrl('app_reservation_view', [
          'id' => $reservation->getId(),
        ])
      ];
    }

    // Encode reservations data
    $data = json_encode($newReservations);

    // Get all reservations in progress
    $reservationsInProgress = $reservationRepository->getReservationsInProgress();

    return $this->render('home.html.twig', [
      'nbApartments' => $number_of_apartments,
      'nbReservations' => $number_of_reservations,
      'totalSales' => $total_sales,
      'reservations' => $data,
      'reservationsInProgress' => $reservationsInProgress,
      'apartments' => $apartments,
    ]);
  }
}