<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ClientRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
  #[Route('/reservations', name: 'app_reservations_list')]
  public function list(ReservationRepository $reservationRepository, ClientRepository $clientRepository)
  {
    $reservations = $reservationRepository->findBy([], ['id' => 'DESC']);
    $clients = $clientRepository->findAll();

    return $this->render('reservations/list.html.twig', [
      'reservations' => $reservations,
      'clients' => $clients,
    ]);
  }

  #[Route('/reservation/view/{id}', name: 'app_reservation_view')]
  public function view(int $id, ReservationRepository $reservationRepository, ClientRepository $clientRepository)
  {
    $reservation = $reservationRepository->find($id);
    $client = $clientRepository->find($reservation->getClient()->getId());

    return $this->render('reservations/view.html.twig', [
      'reservation' => $reservation,
      'client' => $client,
    ]);
  }

  #[Route('/reservation/new', name: 'app_reservation_new')]
  public function new(Request $request, EntityManagerInterface $entityManagerInterface)
  {
    $reservation = new Reservation();

    $form = $this->createForm(ReservationType::class, $reservation);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $reservation = $form->getData();

        $entityManagerInterface->persist($reservation);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Réservation créée avec succès!');

        return $this->redirectToRoute('app_reservations_list');
    }

    return $this->render('reservations/new.html.twig', [
      'form' => $form
    ]);
  }

  #[Route('/reservation/delete/{id}', name: 'app_reservation_delete', requirements: ['id' => '\d+'])]
  public function delete(int $id, ReservationRepository $reservationRepository, EntityManagerInterface $entityManagerInterface)
  {
    $reservation = $reservationRepository->find($id);

    $reservationRepository->remove($reservation);
    $entityManagerInterface->flush();

    return $this->redirectToRoute('app_reservations_list');
  }

  #[Route('/reservation/update/{id}', name: 'app_reservation_update')]
  public function update(int $id, Request $request, EntityManagerInterface $entityManagerInterface, Reservation $reservation)
  {
    $form = $this->createForm(ReservationType::class, $reservation);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $reservation = $form->getData();

        $entityManagerInterface->persist($reservation);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Réservation mise à jour avec succès!');

        return $this->redirectToRoute('app_reservations_list');
    }

    return $this->render('reservations/new.html.twig', [
      'form' => $form
    ]);
  }
}