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
  name: 'app:reservation-folder-complete',
  description: 'Changement du statut de la réservation à "dossier complet" si contrat signé et tous les paiements sont reçus',
)]
class ReservationFolderCompleteCommand extends Command
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
    $idConigurationStateReservationCanceled = $configurationRepository->find('4')->getValue();
    $idConigurationStateReservationFolderCompleted = $configurationRepository->find('6')->getValue();
    $stateFolderCompleted = $reservationStateRespository->find($idConigurationStateReservationFolderCompleted);

    foreach ($reservations as $reservation) {

      $cautionValidated = $reservation->isCautionValidated();
      $arrhesValidated = $reservation->isArrhesValidated();
      $soldeValidated = $reservation->isSoldeValidated();
      $contractFile = $reservation->getContractFile();
      $stateReservation = $reservation->getState()->getId();

      if ($cautionValidated == 1 && $arrhesValidated == 1 && $soldeValidated == 1 && $contractFile !== null && ($stateReservation != ($idConigurationStateReservationFinished || $idConigurationStateReservationCanceled))) {

        $reservation->setState($stateFolderCompleted);
        $em->persist($reservation);
        $em->flush();
        $output->write('Réservation '. $reservation->getId() .' complète');
        return Command::SUCCESS;
      } else {
        $output->write('Aucune réservation complète');
        return Command::FAILURE;
      }
    }
    
  }
}
