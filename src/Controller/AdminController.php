<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    // Route d'affichage de l'espace administrateur
    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Déclaration des variables
        $doctors = $userRepository->findDoctors();

        // Rendu de la page
        return $this->render('admin/index.html.twig', [
            'users' => $doctors,
        ]);
    }

    // Route de création d'un docteur
    #[Route('/admin/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // Déclaration des variables
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $fakeEmailError = false;
        $existingUserError = false;
        $matriculeError = false;
        $existingMatriculeError = false;

        // Controle du clic
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise en forme des propriétés simples
            $user->setFirstName(ucwords($form->get('firstname')->getData()));
            $user->setLastName(strtoupper($form->get('lastname')->getData()));

            // Gestion du regex de l'adresse mail
            if (!filter_var($form->get('email')->getData(), FILTER_VALIDATE_EMAIL)) {
                $fakeEmailError = true;
                return $this->render('admin/new.html.twig', [
                    'user' => $user,
                    'form' => $form,
                    'fakeEmailError' => $fakeEmailError,
                    'existingUserError' => $existingUserError,
                    'matriculeError' => $matriculeError,
                    'existingMatriculeError' => $existingMatriculeError
                ]);
            }

            // Gestion d'adresse mail déjà associée à un compte
            $existingUsers = $userRepository->findAll();
            $existingUsersMails = [];
            foreach ($existingUsers as $key => $existingUser) {
                array_push($existingUsersMails, $existingUser->getEmail());
            }
            foreach ($existingUsersMails as $key => $mail) {
                if ($mail == $form->get('email')->getData()) {
                    $existingUserError = true;
                    return $this->render('admin/new.html.twig', [
                        'user' => $user,
                        'form' => $form,
                        'fakeEmailError' => $fakeEmailError,
                        'existingUserError' => $existingUserError,
                        'matriculeError' => $matriculeError,
                        'existingMatriculeError' => $existingMatriculeError
                    ]);
                }
            }

            // Gestion de la longueure du matricule
            $registrationNumber = $form->get('registration_number')->getData();
            $registrationNumberLength = strlen((string)$registrationNumber);
            if ($registrationNumberLength != 7) {
                $matriculeError = true;
                return $this->render('admin/new.html.twig', [
                    'user' => $user,
                    'form' => $form,
                    'fakeEmailError' => $fakeEmailError,
                    'existingUserError' => $existingUserError,
                    'matriculeError' => $matriculeError,
                    'existingMatriculeError' => $existingMatriculeError
                ]);
            }

            // Gestion de matricule déjà associé à un docteur
            $doctors = $userRepository->findDoctors();
            foreach ($doctors as $key => $doctor) {
                if ($doctor->getRegistrationNumber() == $registrationNumber) {
                    $existingMatriculeError = true;
                    return $this->render('admin/new.html.twig', [
                        'user' => $user,
                        'form' => $form,
                        'fakeEmailError' => $fakeEmailError,
                        'existingUserError' => $existingUserError,
                        'matriculeError' => $matriculeError,
                        'existingMatriculeError' => $existingMatriculeError
                    ]);
                }
            }

            // Hashage du mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            // Définition du rôle de l'utilisateur
            $user->setRoles(['ROLE_DOCTOR']);

            $userRepository->save($user, true);

            // Redirection après la création du docteur
            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire
        return $this->render('admin/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'fakeEmailError' => $fakeEmailError,
            'existingUserError' => $existingUserError,
            'matriculeError' => $matriculeError,
            'existingMatriculeError' => $existingMatriculeError
        ]);
    }

    // Route d'affichage d'un docteur
    #[Route('/admin/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Rendu de la page
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }
}
