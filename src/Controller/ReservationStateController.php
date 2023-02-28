<?php

namespace App\Controller;

use App\Entity\ReservationState;
use App\Form\ReservationStateType;
use App\Repository\ReservationStateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationStateController extends AbstractController
{
  #[Route('/reservation-states/list', name: 'app_reservation_states_list')]
  public function list(ReservationStateRepository $reservationStateRepository)
  {
    $reservationStates = $reservationStateRepository->findAll();

    return $this->render('reservation-states/list.html.twig', [
      'reservationStates' => $reservationStates,
    ]);
  }

  #[Route('/reservation-states/new', name: 'app_reservation_state_new')]
  public function new(Request $request, EntityManagerInterface $entityManagerInterface)
  {
    $reservationState = new ReservationState();

    $form = $this->createForm(ReservationStateType::class, $reservationState);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $reservationState = $form->getData();

        $entityManagerInterface->persist($reservationState);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Statut de réservation créé avec succès!');

        return $this->redirectToRoute('app_reservation_states_list');
    }

    return $this->render('reservation-states/new.html.twig', [
      'form' => $form
    ]);
  }

  #[Route('/reservation-state/delete/{id}', name: 'app_reservation_state_delete', requirements: ['id' => '\d+'])]
  public function delete(int $id, ReservationStateRepository $reservationRepository, EntityManagerInterface $entityManagerInterface)
  {
    $reservationState = $reservationRepository->find($id);

    $reservationRepository->remove($reservationState);
    $entityManagerInterface->flush();

    return $this->redirectToRoute('app_reservation_states_list');
  }

  #[Route('/reservation-state/update/{id}', name: 'app_reservation_state_update')]
  public function update(int $id, Request $request, EntityManagerInterface $entityManagerInterface, ReservationState $reservationState)
  {
    $form = $this->createForm(ReservationStateType::class, $reservationState);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $reservationState = $form->getData();

        $entityManagerInterface->persist($reservationState);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Statut de réservation mis à jour avec succès!');

        return $this->redirectToRoute('app_reservation_states_list');
    }

    return $this->render('reservation-states/new.html.twig', [
      'form' => $form
    ]);
  }
}