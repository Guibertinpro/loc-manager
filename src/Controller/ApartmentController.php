<?php

namespace App\Controller;

use App\Entity\Apartment;
use App\Form\ApartmentType;
use App\Repository\ApartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApartmentController extends AbstractController
{
  #[Route('/apartments', name: 'app_apartments_list')]
  public function list(ApartmentRepository $apartmentRepository)
  {
    $apartments = $apartmentRepository->findAll();

    return $this->render('apartments/list.html.twig', [
      'apartments' => $apartments,
    ]);
  }

  #[Route('/apartment/new', name: 'app_apartment_new')]
  public function new(Request $request, EntityManagerInterface $entityManagerInterface)
  {
    // creates a task object and initializes some data for this example
    $apartment = new Apartment();

    $form = $this->createForm(ApartmentType::class, $apartment);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      
        $apartment = $form->getData();

        $entityManagerInterface->persist($apartment);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Apartement créé avec succès!');

        return $this->redirectToRoute('app_apartments_list');
    }

    return $this->render('apartments/new.html.twig', [
      'form' => $form
    ]);
  }

  #[Route('/apartment/delete/{id}', name: 'app_apartment_delete', requirements: ['id' => '\d+'])]
  public function delete(int $id, ApartmentRepository $apartmentRepository, EntityManagerInterface $entityManagerInterface)
  {
    $apartment = $apartmentRepository->find($id);

    $apartmentRepository->remove($apartment);
    $entityManagerInterface->flush();

    return $this->redirectToRoute('app_apartments_list');
  }

  #[Route('/apartment/update/{id}', name: 'app_apartment_update')]
  public function update(Request $request, EntityManagerInterface $entityManagerInterface, Apartment $apartment)
  {
    $form = $this->createForm(ApartmentType::class, $apartment);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $apartment = $form->getData();

        $entityManagerInterface->persist($apartment);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Apartement mis à jour avec succès!');

        return $this->redirectToRoute('app_apartments_list');
    }

    return $this->render('apartments/new.html.twig', [
      'form' => $form
    ]);
  }
}