<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Form\NoticeType;
use App\Repository\NoticeRepository;
use App\Repository\StayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/doctor/notice')]
class NoticeController extends AbstractController
{
    // Route de création d'un avis
    #[Route('/{id}-new', name: 'app_notice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NoticeRepository $noticeRepository, StayRepository $stayRepository, $id): Response
    {
        // Déclaration des variables
        $stay = $stayRepository->findOneBy(array('id' => $id));
        $notice = new Notice();
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);
        $dateError = false;

        // Controle du clic
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise en forme des propriétés simples
            $notice->setTitle(ucwords($form->get('title')->getData()));
            $notice->setDescription(ucfirst($form->get('description')->getData()));

            // Assignation du séjour
            $notice->setStay($stay);

            // Vérification de la date
            $selectedDate = $form->get('date')->getData();
            $selectedDateFormated = $selectedDate->format("d-m-Y");
            $stayFromDate = $stay->getDateFrom();
            $stayToDate = $stay->getDateTo();
            $stayFromDateFormated = $stayFromDate->format("d-m-Y");
            $stayToDateFormated = $stayToDate->format("d-m-Y");
            
            if ($selectedDateFormated < $stayFromDateFormated || $selectedDateFormated > $stayToDateFormated) {
                $dateError = true;
                return $this->render('notice/new.html.twig', [
                    'stay' => $stay,
                    'notice' => $notice,
                    'form' => $form,
                    'dateError' => $dateError
                ]);
            }

            // Enregistrement de l'instance en BDD
            $noticeRepository->save($notice, true);

            // Redirection après la création de l'avis
            return $this->redirectToRoute('app_main_show', array('id' => $stay->getId()), Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire
        return $this->render('notice/new.html.twig', [
            'stay' => $stay,
            'notice' => $notice,
            'form' => $form,
            'dateError' => $dateError
        ]);
    }
}
