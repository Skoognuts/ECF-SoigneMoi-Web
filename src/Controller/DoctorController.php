<?php

namespace App\Controller;

use App\Repository\StayRepository;
use App\Repository\NoticeRepository;
use App\Repository\PrescriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DoctorController extends AbstractController
{
    // Constructeur de classe
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    // Route d'affichage de l'espace docteur
    #[Route('/doctor', name: 'app_doctor', methods: ['GET'])]
    public function index(StayRepository $stayRepository): Response
    {
        // Déclaration des variables
        $today = new \DateTime('now');
        $stays = $stayRepository->findAllByDoctor($this->currentUser);

        // Tri des rendez-vous
        $incommingStays = [];
        $currentStays = [];
        $pastStays = [];

        foreach ($stays as $key => $stay) {
            $stayFromDate = $stay->getDateFrom();
            $stayToDate = $stay->getDateTo();
            if ($stayFromDate > $today) {
                array_push($incommingStays, $stay);
            } elseif ($stayToDate < $today) {
                array_push($pastStays, $stay);
            } elseif ($stayFromDate <= $today && $stayToDate >= $today) {
                array_push($currentStays, $stay);
            }
        }

        // Rendu de la page
        return $this->render('doctor/index.html.twig', [
            'currentUser' => $this->currentUser,
            'incommingStays' => $incommingStays,
            'currentStays' => $currentStays,
            'pastStays' => $pastStays
        ]);
    }
}
