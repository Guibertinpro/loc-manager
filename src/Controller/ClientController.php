<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
  #[Route('/clients', name: 'app_clients_list')]
  public function list(ClientRepository $clientRepository): Response
  {
    $clients = $clientRepository->findBy([], ['id' => 'DESC']);

    return $this->render('clients/list.html.twig', [
      'clients' => $clients,
    ]);
  }

  #[Route('/client/new', name: 'app_client_new')]
  public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
  {
    $client = new Client();

    $form = $this->createForm(ClientType::class, $client);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $client = $form->getData();

        $entityManagerInterface->persist($client);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Client créé avec succès!');

        return $this->redirectToRoute('app_clients_list');
    }

    return $this->render('clients/new.html.twig', [
      'form' => $form
    ]);
  }

  #[Route('/client/view/{id}', name: 'app_client_view')]
  public function view(int $id, ClientRepository $clientRepository, ReservationRepository $reservationRepository)
  {
    $client = $clientRepository->find($id);
    $clientReservations = $reservationRepository->findBy(['client' => $id], ['id' => 'DESC']);
    $totalReservations = $reservationRepository->getTotalReservationsByClientId($id);
    $totalReservationsSales = $reservationRepository->getTotalReservationsSalesByClientId($id);
    $reservationsInProgress = $reservationRepository->getReservationsInProgressByClientId($id);

    return $this->render('clients/view.html.twig', [
      'client' => $client,
      'clientReservations' => $clientReservations,
      'totalReservations' => $totalReservations,
      'totalReservationsSales' => $totalReservationsSales,
      'reservationsInProgress' => $reservationsInProgress,
    ]);
  }

  #[Route('/client/delete/{id}', name: 'app_client_delete', requirements: ['id' => '\d+'])]
  public function delete(int $id, ClientRepository $clientRepository, EntityManagerInterface $entityManagerInterface)
  {
    $client = $clientRepository->find($id);

    $clientRepository->remove($client);
    $entityManagerInterface->flush();

    return $this->redirectToRoute('app_clients_list');
  }

  #[Route('/client/update/{id}', name: 'app_client_update')]
  public function update(int $id, Request $request, EntityManagerInterface $entityManagerInterface, Client $client)
  {
    $form = $this->createForm(ClientType::class, $client);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $client = $form->getData();

        $entityManagerInterface->persist($client);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Réservation mise à jour avec succès!');

        return $this->redirectToRoute('app_clients_list');
    }

    return $this->render('clients/new.html.twig', [
      'form' => $form
    ]);
  }
}
