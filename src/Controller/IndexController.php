<?php

namespace App\Controller;

use App\Repository\ApartmentRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class IndexController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home(ApartmentRepository $apartmentRepository, ReservationRepository $reservationRepository, ReservationController $reservationController, ChartBuilderInterface $chartBuilder)
  {
    // Get data
    $number_of_reservations = $reservationRepository->getTotalReservations();
    $total_sales = $reservationRepository->getTotalReservationsSales();
    $apartments = $apartmentRepository->findAll();
    $reservations = $reservationRepository->findAll();
    $reservationsInProgress = $reservationRepository->getReservationsInProgress();

    $newReservations = [];
    // Format reservations data
    foreach ($reservations as $reservation) {
      $newReservations[] = [
        'id' => $reservation->getId(),
        'start' => $reservation->getStartAt()->format('Y-m-d H:i'),
        'end' => $reservation->getEndAt()->format('Y-m-d H:i'),
        'title' => $reservation->getClient()->getFullName(),
        'time' => false,
        'backgroundColor' => $reservation->getApartment()->getColor(),
        'textColor' => 'white',
        'url' => $reservationController->generateUrl('app_reservation_view', [
          'id' => $reservation->getId(),
        ])
      ];
    }
    // Encode reservations data
    $reservationsData = json_encode($newReservations);

    // Construct chart for year sales per month
    $months = ['Janvier' => '1', 'Février' => '2', 'Mars' => '3', 'Avril' => '4', 'Mai' => '5', 'Juin' => '6', 'Juillet' => '7', 'Août' => '8', 'Septembre' => '9', 'Octobre' => '10', 'Novembre' => '11', 'Décembre' => '12'];
    $now = new DateTime('now');
    $year = $now->format('Y');
    $datasets = [];
    $i = 0;
      foreach ($apartments as $apartment) {
        $monthData = [];
        foreach ($months as $k => $v) {
          $monthData[] = $reservationRepository->getTotalSalesByYearAndMonth($year, $v, $apartment);
        }
        $datasets[] = [
          'stack' => 'Stack '.$i,
          'label' => $apartment->getName(),
          'backgroundColor' => $apartment->getColor(),
          'data' => $monthData,
        ];
        $i = $i+1;
      }

    // Create chart
    $chartYear = $chartBuilder->createChart(Chart::TYPE_BAR);
    $chartYear->setData([
        'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        'datasets' => $datasets,
    ]);

    $chartYear->setOptions([
        'scales' => [
            'y' => [
                'suggestedMin' => 0,
                'suggestedMax' => 100,
            ],
        ],
        'responsive' => true,
        'maintainAspectRatio' => false,
    ]);

    return $this->render('home.html.twig', [
      'nbReservations' => $number_of_reservations,
      'totalSales' => $total_sales,
      'reservations' => $reservationsData,
      'reservationsInProgress' => $reservationsInProgress,
      'apartments' => $apartments,
      'chartYear' => $chartYear
    ]);
  }
}