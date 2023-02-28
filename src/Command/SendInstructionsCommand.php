<?php

namespace App\Command;

use App\Entity\Reservation;
use App\Entity\ReservationState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
  name: 'app:send-instructions',
  description: 'Envoi des consignes par mail 1 semaine avant la date d\'arrivée',
)]
class SendInstructionsCommand extends Command
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
    $stateOk = $reservationStateRespository->find('11');

    foreach ($reservations as $reservation) {
      $startAt = $reservation->getStartAt();
      $startAtDay = $startAt->format('d');
      $startAtMonth = $startAt->format('m');

      $now = new \DateTime('now');
      $nowDay = $now->format('d');
      $nowMonth = $now->format('m');

      if ($startAtDay == $nowDay && $startAtMonth == $nowMonth) {

        $reservation->setState($stateOk);
        $em->persist($reservation);
        $em->flush();
        $output->write('Réservations '. $reservation->getId() .' en cours');
      } else {
        
      }
    }
    $output->write('Aucune réservation en cours');
    
    return Command::SUCCESS;
  }
}
