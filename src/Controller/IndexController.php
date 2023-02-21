<?php

namespace App\Controller;

use App\Repository\ApartmentRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home(ApartmentRepository $apartmentRepository, ReservationRepository $reservationRepository)
  {
    $number_of_apartments = $apartmentRepository->getTotalApartments();
    $number_of_reservations = $reservationRepository->getTotalReservations();
    $total_sales = $reservationRepository->getTotalReservationsSales();

    $reservations = $reservationRepository->findAll();
    $newReservations = [];

    foreach ($reservations as $reservation) {
      $newReservations[] = [
        'id' => $reservation->getId(),
        'start' => $reservation->getStartAt()->format('Y-m-d'),
        'end' => $reservation->getEndAt()->format('Y-m-d'),
        'title' => $reservation->getFullName(),
        'backgroundColor' => $reservation->getState()->getColor(),
        'textColor' => 'white'
      ];
    }

    $data = json_encode($newReservations);

    return $this->render('home.html.twig', [
      'nbApartments' => $number_of_apartments,
      'nbReservations' => $number_of_reservations,
      'totalSales' => $total_sales,
      'reservations' => $data
    ]);
  }
}