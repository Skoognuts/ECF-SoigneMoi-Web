<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirection automatique en fonction du rôle
        if ($this->getUser()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('app_admin');
            } else if (in_array('ROLE_DOCTOR', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('app_doctor');
            } else if (in_array('ROLE_SECRETARY', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('app_access_denied');
            } else if (in_array('ROLE_USER', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('app_main');
            }
        }

        // Redirection apres erreur d'authentification
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            return $this->redirectToRoute('app_login_error', [], Response::HTTP_SEE_OTHER);
        }

        // Configuration du formulaire de connexion avec les derniers identifiants connus
        $lastUsername = $authenticationUtils->getLastUsername();

        // Rendu de la page 
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    // Route d'accès refusé
    #[Route('/access-denied', name: 'app_access_denied')]
    public function index(): Response
    {
        // Rendu de la page 
        return $this->render('security/access_denied.html.twig', []);
    }

    // Route de déconnexion
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // Redirection vers la page d'accueil
        return $this->redirectToRoute('app_landing_page', [], Response::HTTP_SEE_OTHER);
    }

    // Route d'erreur d'identifiants (temporisation)
    #[Route(path: '/login-error', name: 'app_login_error')]
    public function loginError(): Response
    {
        // Rendu de la page 
        return $this->render('security/login_error.html.twig');
    }
}
