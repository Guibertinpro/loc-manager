<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
  #[Route('/reservations/list', name: 'app_reservations_list')]
  public function list()
  {
      return $this->render('reservations/list.html.twig');
  }

  #[Route('/reservation/new', name: 'app_reservation_new')]
  public function new()
  {
      return $this->render('reservations/new.html.twig');
  }
}