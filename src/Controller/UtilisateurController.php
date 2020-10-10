<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UtilisateurController extends AbstractController
{
    public function index()
    {
        return $this->render('utilisateur/index.html.twig',
            [
                'controller_name' => 'UtilisateurController',

            ]);
    }

    /**
     * @Route("/")
     */
    public function connexion(AuthenticationUtils $authenticationUtils)
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        if (!empty($error)) {

            $this->addFlash('error', 'Identifiants incorrects');
        }

        return $this->render('utilisateur/connexion.html.twig', [
            'last_username' => $lastUsername
        ]);

    }

    /**
     * @Route("/deconnexion")
     */
    public function deconnexion()
    {
        // cette méthode est vide car sa route est configurée dans la partie
        // logout de config/packages/security.yaml
    }

}
