<?php

namespace App\Controller;

use App\Form\ConfigurationType;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigurationController extends AbstractController
{
  #[Route('/configuration', name: 'app_configuration_list')]
  public function index(ConfigurationRepository $configurationRepository): Response
  {
    $configurations = $configurationRepository->findAll();

    return $this->render('configuration/list.html.twig', [
      'controller_name' => 'ConfigurationController',
      'configurations' => $configurations,
    ]);
  }

  #[Route('/configuration/edit/{id}', name: 'app_configuration_edit', requirements: ['id' => '\d+'])]
  public function edit(int $id, Request $request, EntityManagerInterface $entityManagerInterface, ConfigurationRepository $configurationRepository): Response
  {
    $configuration = $configurationRepository->find($id);

    $form = $this->createForm(ConfigurationType::class, $configuration);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $configuration = $form->getData();

        $entityManagerInterface->persist($configuration);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Configuration modifiée avec succès!');

        return $this->redirectToRoute('app_configuration_list');
    }

    return $this->render('configuration/edit.html.twig', [
      'configuration' => $configuration,
      'form' => $form
    ]);
  }
}
