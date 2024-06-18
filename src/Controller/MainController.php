<?php

namespace App\Controller;

use App\Entity\Stay;
use App\Form\StayType;
use App\Repository\StayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/main')]
class MainController extends AbstractController
{
    // Constructeur de classe
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    // Route d'affichage de l'espace utilisateur
    #[Route('/', name: 'app_main', methods: ['GET'])]
    public function index(StayRepository $stayRepository): Response
    {
        // Déclaration des variables
        $userStays = $stayRepository->findAllByUser($this->currentUser);

        // Tri des rendez-vous
        $incommingStays = [];
        $currentStays = [];
        $pastStays = [];

        // Rendu de la page
        return $this->render('main/index.html.twig', [
            'currentUser' => $this->currentUser,
            'incommingStays' => $incommingStays,
            'currentStays' => $currentStays,
            'pastStays' => $pastStays
        ]);
    }

    #[Route('/new', name: 'app_main_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stay = new Stay();
        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stay);
            $entityManager->flush();

            return $this->redirectToRoute('app_main_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/new.html.twig', [
            'stay' => $stay,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_show', methods: ['GET'])]
    public function show(Stay $stay): Response
    {
        return $this->render('main/show.html.twig', [
            'stay' => $stay,
        ]);
    }

    #[Route('/{id}', name: 'app_main_delete', methods: ['POST'])]
    public function delete(Request $request, Stay $stay, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stay->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stay);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_main_index', [], Response::HTTP_SEE_OTHER);
    }
}
