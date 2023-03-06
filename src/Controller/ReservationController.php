<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ClientRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\ContractFileRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReservationStateRepository;
use App\Service\PdfService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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

      $reservation->setCautionValidated(false);
      $reservation->setArrhesValidated(false);
      $reservation->setSoldeValidated(false);

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
    $apartment = strtolower($reservation->getApartment()->getName());
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

    // Get the good template
    $template = null;
    switch ($apartment) {
      case 'capbreton':
        $template = 'pdf/capbreton.html.twig';
        break;
      case 'carnac':
        $template = 'pdf/carnac.html.twig';
        break;
      case 'valmorel':
        $template = 'pdf/valmorel.html.twig';
        break;
      case 'moliets':
        $template = 'pdf/moliets.html.twig';
        break;
      
      default:
        $template = 'pdf/pdf-layout.html.twig';
        break;
    }

    $html =  $this->renderView($template, $data);
    $pdfService->showPdfFile($html, $reservation->getId());
  }

  #[Route('reservation/send-contract/{id}', name: 'app_reservation_send_contract', requirements: ['id' => '\d+'])]
  public function sendContract(int $id, ReservationRepository $reservationRepository, PdfService $pdfService, MailerInterface $mailer, EntityManagerInterface $em, ConfigurationRepository $conf, ReservationStateRepository $reservationStateRepository)
  {
    $reservation = $reservationRepository->find($id);
    $apartment = strtolower($reservation->getApartment()->getName());
    $client = $reservation->getClient()->getId();
    $clientEmail = $reservation->getClient()->getEmail();
    $emailAdmin = $conf->find('1')->getValue();
    $idStateContractSend = $conf->find('7')->getValue();
    $stateContractSend = $reservationStateRepository->find($idStateContractSend);


    // Get images
    $iban = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/iban-boursorama.jpg');
    $logo = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/logo-sejours-evasion.png');
    $logoApartment = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/' . str_replace(" ", "", $apartment) . '.jpeg');

    $dateNow = new DateTime('now');

    $data = [
      'reservation' => $reservation,
      'client' => $client,
      'iban' => $iban,
      'logo' => $logo,
      'now' => $dateNow,
      'logoApartment' => $logoApartment,
    ];

    // Get the good template
    $pdfTemplate = null;
    switch ($apartment) {
      case 'capbreton':
        $pdfTemplate = 'pdf/capbreton.html.twig';
        break;
      case 'carnac':
        $pdfTemplate = 'pdf/carnac.html.twig';
        break;
      case 'valmorel':
        $pdfTemplate = 'pdf/valmorel.html.twig';
        break;
      case 'moliets':
        $pdfTemplate = 'pdf/moliets.html.twig';
        break;
      
      default:
        $pdfTemplate = 'pdf/pdf-layout.html.twig';
        break;
    }

    $html =  $this->renderView($pdfTemplate, $data);
    $pdfService->generateAndSavePdfContract($html, $id);

    // Get the pdf file
    $contractPDF = $pdfService->getPdfContract($id);

    $templateEmail = 'emails/send-contract/base.html.twig';
    $email = (new TemplatedEmail())
      ->from(new Address($emailAdmin, 'Séjour evasion'))
      ->to($clientEmail)
      ->cc(new Address($emailAdmin, 'Séjour evasion'))
      ->subject('Votre demande de séjour à ' . $reservation->getApartment()->getName())
      ->addPart(new DataPart(new File($contractPDF), 'Contrat de réservation n°' . $id, 'application/pdf'))
      ->htmlTemplate($templateEmail)
      ->context([
        'reservation' => $reservation,
      ]);

    try {
      $mailer->send($email);
      $reservation->setState($stateContractSend);
      $em->persist($reservation);
      $em->flush();

      $pdfService->removePdfContract($id);

      return $this->redirectToRoute('app_reservation_view', ['id' => $id]);

    } catch (TransportExceptionInterface $e) {
      echo $e->getMessage();
    }
  }

  #[Route('reservation/validate-payment/{id}', name: 'app_ajax_validate_payment_type', requirements: ['id' => '\d+'])]
  public function ajaxValidatePaymentType(Request $request, int $id, ReservationRepository $reservationRepository)
  {
    $reservation = $reservationRepository->find($id);

    $paymentName = $request->get('paymentName');
    $paymentSetMethod = 'set'.ucfirst($paymentName).'Validated';
    $newPaymentValue = $request->get('paymentValue');

    if ($newPaymentValue === 'false') {
      $newPaymentValue = 0;
    } else {
      $newPaymentValue = 1;
    }

    $reservation->$paymentSetMethod($newPaymentValue);

    $reservationRepository->save($reservation, true);

    $data = [$paymentSetMethod, $newPaymentValue];

    return new JsonResponse($data);
  }
}