<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Stay;
use App\Form\StayType;
use App\Form\DoctorChoiceType;
use App\Repository\UserRepository;
use App\Repository\StayRepository;
use App\Repository\SpecialtyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $today = new \DateTime('now');
        $userStays = $stayRepository->findAllByUser($this->currentUser);

        // Tri des rendez-vous
        $incommingStays = [];
        $currentStays = [];
        $pastStays = [];

        foreach ($userStays as $key => $stay) {
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
        return $this->render('main/index.html.twig', [
            'currentUser' => $this->currentUser,
            'incommingStays' => $incommingStays,
            'currentStays' => $currentStays,
            'pastStays' => $pastStays
        ]);
    }

    // Route de création de séjour
    #[Route('/new-stay', name: 'app_main_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StayRepository $stayRepository): Response
    {
        // Déclaration des variables
        $today = new \DateTime('now');
        $stay = new Stay();
        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);
        $dateOrderError = false;
        $pastDateError = false;

        // Controle du clic
        if ($form->isSubmitted() && $form->isValid()) {
            $stayFromDate = $form->get('date_from')->getData();
            $stayToDate = $form->get('date_to')->getData();
            $specialty = $form->get('specialty')->getData();
            // Verifier si l'ordre des dates est bon
            if ($stayFromDate > $stayToDate) {
                $dateOrderError = true;
                return $this->render('main/new.html.twig', [
                    'currentUser' => $this->currentUser,
                    'stay' => $stay,
                    'form' => $form,
                    'dateOrderError' => $dateOrderError,
                    'pastDateError' => $pastDateError
                ]);
            }

            // Vérifier que les dates ne soient pas passées
            if ($stayFromDate <= $today) {
                $pastDateError = true;
                return $this->render('main/new.html.twig', [
                    'currentUser' => $this->currentUser,
                    'stay' => $stay,
                    'form' => $form,
                    'dateOrderError' => $dateOrderError,
                    'pastDateError' => $pastDateError
                ]);
            }

            // Formatage du motif
            $stay->setReason(ucwords($form->get('reason')->getData()));

            // Affecter l'utilisateur
            $stay->setUser($this->currentUser);

            // Affecter les dates
            $stay->setDateFrom($stayFromDate);
            $stay->setDateTo($stayToDate);

            // Enregistrement de l'instance en BDD
            $stayRepository->save($stay, true);

            // Redirection après la création du séjour
            return $this->redirectToRoute('app_main_choose_doctor', array('id' => $stay->getId(), 'specialty' => $specialty->getId()), Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire
        return $this->render('main/new.html.twig', [
            'currentUser' => $this->currentUser,
            'stay' => $stay,
            'form' => $form,
            'dateOrderError' => $dateOrderError,
            'pastDateError' => $pastDateError
        ]);
    }

    // Route d'affectation du docteur au séjour
    #[Route('/new-stay-{id}/choose-doctor-{specialty}', name: 'app_main_choose_doctor', methods: ['GET', 'POST'])]
    public function newChooseDoctor(Request $request, UserRepository $userRepository, StayRepository $stayRepository, SpecialtyRepository $specialtyRepository, $id, $specialty): Response
    {
        // Déclaration des variables
        $stay = $stayRepository->findOneBy(array('id' => $id));
        $doctors = $userRepository->findBy(array('specialty' => $specialty));
        $formBuilder = $this->createFormBuilder($stay)
            ->add('doctor', EntityType::class, [
                'required' => true,
                'mapped' => false,
                'class' => User::class,
                'label' => 'Docteur',
                'choices' => $doctors,
                'placeholder' => 'Choisir un Docteur',
                'attr' => [
                    'class' => 'form-text-input required'
                ]
            ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // Controle du clic
        if ($form->isSubmitted() && $form->isValid()) {
            // Affecter le docteur
            $stay->setDoctor($form->get('doctor')->getData());

            // Enregistrement de l'instance en BDD
            $stayRepository->save($stay, true);

            // Redirection après l'affectation du docteur au séjour'
            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire
        return $this->render('main/doctor_choice.html.twig', [
            'currentUser' => $this->currentUser,
            'stay' => $stay,
            'form' => $form
        ]);
    }

    // Route d'affichage d'un séjour
    #[Route('/{id}', name: 'app_main_show', methods: ['GET'])]
    public function show(Stay $stay): Response
    {
        // Déclaration des variables
        $today = new \DateTime('now');
        $isNoticeAndPrescriptionPossible = false;
        $notices = $stay->getNotices();
        $prescriptions = $stay->getPrescriptions();

        // Vérificattion de la plage de date
        $stayFromDate = $stay->getDateFrom();
        $stayToDate = $stay->getDateTo();

        if ($stayFromDate <= $today && $stayToDate >= $today) {
            $isNoticeAndPrescriptionPossible = true;
        }

        // Rendu de la page
        return $this->render('main/show.html.twig', [
            'currentUser' => $this->currentUser,
            'today' => $today,
            'stay' => $stay,
            'notices' => $notices,
            'prescriptions' => $prescriptions,
            'isNoticeAndPrescriptionPossible' => $isNoticeAndPrescriptionPossible
        ]);
    }

    // Route de suppression d'un séjour
    #[Route('/{id}', name: 'app_main_delete', methods: ['POST'])]
    public function delete(Request $request, Stay $stay, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stay->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stay);
            $entityManager->flush();
        }

        // Redirection après suppression
        return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
    }
}
