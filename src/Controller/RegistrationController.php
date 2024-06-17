<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    // Route de création d'un nouveau compte utilisateur
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        // Déclaration des variables
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $fakeEmailError = false;
        $existingUserError = false;

        // Contrôle du clic
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise en forme des propriétés simples
            $user->setFirstName(ucwords($form->get('firstname')->getData()));
            $user->setLastName(strtoupper($form->get('lastname')->getData()));
            $user->setAddress(ucwords($form->get('address')->getData()));

            // Gestion du regex de l'adresse mail
            if (!filter_var($form->get('email')->getData(), FILTER_VALIDATE_EMAIL)) {
                $fakeEmailError = true;
                return $this->render('registration/register.html.twig', [
                    'fakeEmailError' => $fakeEmailError,
                    'existingUserError' => $existingUserError,
                    'registrationForm' => $form,
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
                    return $this->render('registration/register.html.twig', [
                        'fakeEmailError' => $fakeEmailError,
                        'existingUserError' => $existingUserError,
                        'registrationForm' => $form,
                    ]);
                }
            }

            // Hashage du mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            // Définition du rôle de l'utilisateur
            $user->setRoles(['ROLE_USER']);

            // Enregistrement de l'utilisateur en BDD
            $userRepository->save($user, true);

            // Redirection après la création de l'utilisateur
            return $this->redirectToRoute('app_login');
        }

        // Rendu du formulaire
        return $this->render('registration/register.html.twig', [
            'fakeEmailError' => $fakeEmailError,
            'existingUserError' => $existingUserError,
            'registrationForm' => $form,
        ]);
    }
}
