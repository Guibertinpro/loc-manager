<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ClientRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\ContractFileRepository;
use App\Repository\ReservationRepository;
use App\Service\PdfService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
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

  #[Route('/reservation/new', name: 'app_reservation_new')]
  public function new(Request $request, EntityManagerInterface $entityManagerInterface)
  {
    $reservation = new Reservation();

    $form = $this->createForm(ReservationType::class, $reservation);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $reservation = $form->getData();

        $dateLeftToPay = $reservation->getStartAt()->modify("-1 month");
        $reservation->setDateLeftToPay($dateLeftToPay);

        $entityManagerInterface->persist($reservation);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Réservation créée avec succès!');

        return $this->redirectToRoute('app_reservation_view', ['id' => $reservation->getId()]);
    }

    return $this->render('reservations/new.html.twig', [
      'form' => $form
    ]);
  }

  #[Route('/reservation/view/{id}', name: 'app_reservation_view', requirements: ['id' => '\d+'])]
  public function view(int $id, ReservationRepository $reservationRepository, ClientRepository $clientRepository, ContractFileRepository $contractFileRepository)
  {
    $reservation = $reservationRepository->find($id);
    $client = $clientRepository->find($reservation->getClient()->getId());
    $contract = $contractFileRepository->findBy(['reservation' => $id]);

    return $this->render('reservations/view.html.twig', [
      'reservation' => $reservation,
      'client' => $client,
      'contract' => $contract
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

  #[Route('/reservation/update/{id}', name: 'app_reservation_update', requirements: ['id' => '\d+'])]
  public function update(Request $request, EntityManagerInterface $entityManagerInterface, Reservation $reservation)
  {
    $form = $this->createForm(ReservationType::class, $reservation);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $reservation = $form->getData();

        $dateStart = $reservation->getStartAt();
        $dateLeftToPay = clone $dateStart;
        $dateLeftToPay = $dateLeftToPay->modify("-1 month");
        $reservation->setDateLeftToPay($dateLeftToPay);

        $entityManagerInterface->persist($reservation);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Réservation mise à jour avec succès!');

        return $this->redirectToRoute('app_reservations_list');
    }

    return $this->render('reservations/new.html.twig', [
      'form' => $form
    ]);
  }

  #[Route('reservation/pdf/{id}', name: 'app_reservation_pdf_download', requirements: ['id' => '\d+'])]
  public function generatePdfContract(int $id, ReservationRepository $reservationRepository, ClientRepository $clientRepository, PdfService $pdfService)
  {
    $reservation = $reservationRepository->find($id);
    $client = $clientRepository->find($reservation->getClient()->getId());

    // Get images
    $iban = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/iban-boursorama.jpg');

    $dateNow = new DateTime('now');

    $data = [
      'reservation' => $reservation,
      'client' => $client,
      'iban' => $iban,
      'now' => $dateNow,
    ];
    $html =  $this->renderView('pdf/pdf-layout.html.twig', $data);
    $pdfService->showPdfFile($html, $reservation->getId());
  }

  #[Route('reservation/{id}/test-mail', name: 'app_reservation_test_mail', requirements: ['id' => '\d+'])]
  public function testMail(int $id, ReservationRepository $reservationRepository, MailerInterface $mailer, ConfigurationRepository $configurationRepository): Response
  {
    $emailAdmin = $configurationRepository->find('1')->getValue();
    $clientEmail = $reservationRepository->find($id)->getClient()->getEmail();
    $apartment = strtolower($reservationRepository->find($id)->getApartment()->getName());
    
    $file = 'consignes-' . $apartment . '.pdf';
    $filePath = $this->getParameter('kernel.project_dir'). '/public/uploads/instructions/' . $file;

    dump($emailAdmin);
    dump($clientEmail);

    $email = (new Email())
      ->from($emailAdmin)
      ->to($clientEmail)
      ->subject('Test Email!')
      ->text('Sending emails test!')
      ->addPart(new DataPart(new File($filePath), 'Consignes du séjour'))
      ->html('<p>Sending email test!</p>');

    try {
      //code...
      $mailer->send($email);
    } catch (TransportExceptionInterface $e) {
      echo $e->getMessage();
    }

    return $this->redirectToRoute('app_reservation_view', ['id' => $id]);

  }
}