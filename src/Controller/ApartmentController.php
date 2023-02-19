<?php

namespace App\Controller;

use App\Entity\Apartment;
use App\Form\ApartmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApartmentController extends AbstractController
{
  #[Route('/apartments/list', name: 'app_apartments_list')]
  public function list()
  {
    return $this->render('apartments/list.html.twig');
  }

  #[Route('/apartment/new', name: 'app_apartment_new')]
  public function new(Request $request, EntityManagerInterface $entityManagerInterface)
  {
    // creates a task object and initializes some data for this example
    $apartment = new Apartment();

    $form = $this->createForm(ApartmentType::class, $apartment);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        $apartment = $form->getData();

        $entityManagerInterface->persist($apartment);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('app_home');
    }

    return $this->render('apartments/new.html.twig', [
      'form' => $form
    ]);
  }
}