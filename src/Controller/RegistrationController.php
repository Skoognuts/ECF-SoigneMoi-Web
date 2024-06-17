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
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        // Déclaration des variables
        $sameEmailError = false;

        // Création du formulaire
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Contrôle du clic
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise en forme des propriétés simples
            $user->setFirstName(ucwords($form->get('firstname')->getData()));
            $user->setLastName(strtoupper($form->get('lastname')->getData()));
            $user->setAddress(ucwords($form->get('address')->getData()));
            
            // Gestion d'adresse déjà associée à un compte
            $existingUsers = $userRepository->findAll();
            foreach ($existingUsers as $key => $existingUser) {
                if ($existingUser->getEmail() == $form->get('email')->getData()) {
                    $sameEmailError = true;
                    return $this->render('registration/register.html.twig', [
                        'sameEmailError' => $sameEmailError,
                        'registrationForm' => $form,
                    ]);
                } else {
                    $user->setEmail($form->get('email')->getData());
                }
            }

            // Hashage du mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            // Définition du rôle de l'utilisateur
            $user->setRoles('ROLE_USER');

            // Enregistrement de l'utilisateur en BDD
            $userRepository->save($user, true);

            // Redirection après la création de l'utilisateur
            return $this->redirectToRoute('app_login');
        }

        // Rendu du formulaire
        return $this->render('registration/register.html.twig', [
            'sameEmailError' => $sameEmailError,
            'registrationForm' => $form,
        ]);
    }
}
