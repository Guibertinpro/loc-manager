<?php

namespace App\Command;

use App\Entity\Configuration;
use App\Entity\Reservation;
use App\Entity\ReservationState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
  name: 'app:reservation-finished',
  description: 'Changement du statut de la réservation à "terminé" quand le jour de départ correspond au jour actuel',
)]
class ReservationFinishedCommand extends Command
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    // 3. Update the value of the private entityManager variable through injection
    $this->entityManager = $entityManager;

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

    $reservationStateRespository = $em->getRepository(ReservationState::class);
    $configurationRepository = $em->getRepository(Configuration::class);
    $idConigurationStateReservationFinished = $configurationRepository->find('3')->getValue();
    $stateOk = $reservationStateRespository->find($idConigurationStateReservationFinished);

    foreach ($reservations as $reservation) {
      $endAt = $reservation->getEndAt();
      $endAtDay = $endAt->format('d');
      $endAtMonth = $endAt->format('m');
      $endAtYear = $endAt->format('Y');

      $now = new \DateTime('now');
      $nowDay = $now->format('d');
      $nowMonth = $now->format('m');
      $nowYear = $now->format('Y');

      if ($endAtDay == $nowDay && $endAtMonth == $nowMonth && $endAtYear == $nowYear) {

        $reservation->setState($stateOk);
        $em->persist($reservation);
        $em->flush();

        $output->write('Réservation n°'. $reservation->getId() .' terminée');
        return Command::SUCCESS;
      } else {
        $output->write('Aucune réservation terminée le ' . $nowDay . '/' . $nowMonth . '/' . $nowYear);
        return Command::FAILURE;
      }
    }
    
    
  }
}
