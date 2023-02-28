<?php

namespace App\Controller;

use App\Entity\ContractFile;
use App\Form\ContractFileType;
use App\Repository\ContractFileRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ContractFileController extends AbstractController
{
    #[Route('/contract-file/{id}', name: 'app_contract_file_new', requirements: ['id' => '\d+'])]
    public function new(int $id, Request $request, EntityManagerInterface $entityManagerInterface, ReservationRepository $reservationRepository, SluggerInterface $slugger)
    {
        $contractFile = new ContractFile();
        $form = $this->createForm(ContractFileType::class, $contractFile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $document = $form->get('filename')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the file must be processed only when a file is uploaded
            if ($document) {
                $originalFilename = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-RES-'.$id.'.'.$document->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $document->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/contracts',
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo $e->getMessage('erreur');
                }

                // updates the 'contractFilename' property to store the file name
                // instead of its contents
                $contractFile->setFilename($newFilename);
            }

            $contractFile->setReservation($reservationRepository->find($id));

            $entityManagerInterface->persist($contractFile);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_reservation_view', ['id' => $contractFile->getReservation()->getId()]);
        }

        return $this->render('contracts/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/contract-file/update/{id}/{reservationId}', name: 'app_contract_file_update', requirements: ['id' => '\d+'])]
    public function update(int $id, int $reservationId,Request $request, EntityManagerInterface $entityManagerInterface, ReservationRepository $reservationRepository, SluggerInterface $slugger, ContractFileRepository $contractFileRepository)
    {
        $contractFile = new ContractFile();
        $form = $this->createForm(ContractFileType::class, $contractFile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $document = $form->get('filename')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the file must be processed only when a file is uploaded
            if ($document) {
                $originalFilename = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-RES-'.$reservationId.'.'.$document->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $document->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/contracts',
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo $e->getMessage('erreur');
                }

                // updates the 'contractFilename' property to store the file name
                // instead of its contents
                $contractFile->setFilename($newFilename);
            }

            // Deleted the old one before uploading the new one
            $oldContractFile = $contractFileRepository->find($id);
            $entityManagerInterface->remove($oldContractFile);
            $entityManagerInterface->flush();
            $filesystem = new Filesystem();
            $filesystem->remove($this->getParameter('kernel.project_dir').'/public/uploads/contracts/'.$oldContractFile->getFilename());

            $contractFile->setReservation($reservationRepository->find($reservationId));

            $entityManagerInterface->persist($contractFile);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_reservation_view', ['id' => $contractFile->getReservation()->getId()]);
        }

        return $this->render('contracts/new.html.twig', [
            'form' => $form
        ]);
    }
}
