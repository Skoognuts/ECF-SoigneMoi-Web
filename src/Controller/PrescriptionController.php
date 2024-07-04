<?php

namespace App\Controller;

use App\Entity\Prescription;
use App\Form\PrescriptionType;
use App\Repository\PrescriptionRepository;
use App\Repository\StayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/doctor/prescription')]
class PrescriptionController extends AbstractController
{
    // Route de création d'une prescription
    #[Route('/{id}-new', name: 'app_prescription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PrescriptionRepository $prescriptionRepository, StayRepository $stayRepository, $id): Response
    {
        // Déclaration des variables
        $today = new \DateTime('now');
        $stay = $stayRepository->findOneBy(array('id' => $id));
        $prescription = new Prescription();
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);
        $dateOrderError = false;
        $pastDateError = false;

        // Controle du clic
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise en forme des propriétés simples
            $medication = $form->get('medication')->getData();
            $quantity = $form->get('quantity')->getData();
            $prescription->setMedication([$quantity, $medication]);
            
            // Verifier si l'ordre des dates est bon
            $prescriptionFromDate = $form->get('date_from')->getData();
            $prescriptionToDate = $form->get('date_to')->getData();
            if ($prescriptionFromDate > $prescriptionToDate) {
                $dateOrderError = true;
                return $this->render('prescription/new.html.twig', [
                    'stay' => $stay,
                    'prescription' => $prescription,
                    'form' => $form,
                    'dateOrderError' => $dateOrderError,
                    'pastDateError' => $pastDateError
                ]);
            }

            // Vérifier que les dates ne soient pas passées
            if ($prescriptionFromDate < $today) {
                $pastDateError = true;
                return $this->render('prescription/new.html.twig', [
                    'stay' => $stay,
                    'prescription' => $prescription,
                    'form' => $form,
                    'dateOrderError' => $dateOrderError,
                    'pastDateError' => $pastDateError
                ]);
            }

            // Assignation du séjour
            $prescription->setStay($stay);

            // Enregistrement de l'instance en BDD
            $prescriptionRepository->save($prescription, true);

            // Redirection après la création de la prescription
            return $this->redirectToRoute('app_main_show', array('id' => $stay->getId()), Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire
        return $this->render('prescription/new.html.twig', [
            'stay' => $stay,
            'prescription' => $prescription,
            'form' => $form,
            'dateOrderError' => $dateOrderError,
            'pastDateError' => $pastDateError
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prescription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_doctor', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prescription/edit.html.twig', [
            'prescription' => $prescription,
            'form' => $form,
        ]);
    }
}
