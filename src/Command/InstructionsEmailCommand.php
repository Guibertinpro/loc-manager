<?php

namespace App\Command;

use App\Entity\Configuration;
use App\Entity\Reservation;
use App\Entity\ReservationState;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

#[AsCommand(
  name: 'app:instructions-email',
  description: 'Envoi des consignes par email',
)]
class InstructionsEmailCommand extends Command
{
  private $entityManager;
  private $projectDir;
  private $mailer;

  public function __construct(EntityManagerInterface $entityManager, $projectDir, MailerInterface $mailer)
  {
    // 3. Update the value of the private entityManager variable through injection
    $this->entityManager = $entityManager;
    $this->projectDir = $projectDir;
    $this->mailer = $mailer;

    parent::__construct();
  }

  protected function configure(): void
  {
    
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $em = $this->entityManager;

    $reservationRepository = $em->getRepository(Reservation::class);
    $reservations = $reservationRepository->findAll();

    $reservationStateRepository = $em->getRepository(ReservationState::class);
    $configurationRepository = $em->getRepository(Configuration::class);
    $idConfigurationStateInstructionsSend = $configurationRepository->find('5')->getValue();
    $idConfigurationStateCompleteFolder = $configurationRepository->find('6')->getValue();
    $stateInstructionsSend = $reservationStateRepository->find($idConfigurationStateInstructionsSend);

    $configurationRepository = $em->getRepository(Configuration::class);

    foreach ($reservations as $reservation) {

      // Get the date 7 days before reservate start
      $startAt = $reservation->getStartAt();
      $newStartAt = $startAt->modify('-7 days');
      $newStartAtDay = $newStartAt->format('d');
      $newStartAtMonth = $newStartAt->format('m');
      $newStartAtYear = $newStartAt->format('Y');

      $stateReservation = $reservation->getState()->getId();

      // Get date now
      $dateSending = new DateTime('now');
      $dateDay = $dateSending->format('d');
      $dateMonth = $dateSending->format('m');
      $dateYear = $dateSending->format('Y');

      // Compare dates and verify if reservation foler is complete
      if ($newStartAtDay == $dateDay &&
          $newStartAtMonth == $dateMonth &&
          $newStartAtYear == $dateYear &&
          $stateReservation == $idConfigurationStateCompleteFolder
          ) {

        $emailAdmin = $configurationRepository->find('1')->getValue();
        $clientEmail = $reservationRepository->find($reservation->getId())->getClient()->getEmail();
        $apartment = strtolower($reservationRepository->find($reservation->getId())->getApartment()->getName());
        
        $file = 'consignes-' . $apartment . '.pdf';
        $filePath = $this->projectDir . '/public/uploads/instructions/' . $file;

        // Get the good template
        $template = null;
        switch ($apartment) {
          case 'capbreton':
            $template = 'emails/send-instructions/capbreton.html.twig';
            break;
          case 'carnac':
            $template = 'emails/send-instructions/carnac.html.twig';
            break;
          case 'valmorel':
            $template = 'emails/send-instructions/valmorel.html.twig';
            break;
          case 'moliets':
            $template = 'emails/send-instructions/moliets.html.twig';
            break;
          
          default:
            $template = 'emails/send-instructions/base.html.twig';
            break;
        }

        $email = (new TemplatedEmail())
          ->from(new Address($emailAdmin, 'Séjours evasion'))
          ->to($clientEmail)
          ->subject('Votre séjour à ' . ucfirst($reservation->getApartment()->getName()) . ' démarre bientôt')
          ->addPart(new DataPart(new File($filePath), 'Consignes du séjour'))
          ->htmlTemplate($template)
          ->context([
            'reservation' => $reservation,
          ]);

        try {
          $this->mailer->send($email);
          $reservation->setState($stateInstructionsSend);
          $em->persist($reservation);
          $em->flush();
        } catch (TransportExceptionInterface $e) {
          echo $e->getMessage();
        }
        $output->write('Consignes envoyées pour la réservation n°' . $reservation->getId(). '.');
        return Command::SUCCESS;
        
      } else {

        $output->write('Aucun envoi à effectué ou réservation annulée');
        return Command::FAILURE;
      }
    }

    
  }
}
