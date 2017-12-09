<?php
/**
 * Created by PhpStorm.
 * User: Luc
 * Date: 20/11/2017
 * Time: 15:42
 */

// src/OC/UserBundle/Controller/SecurityController.php;

namespace LL\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as FosController;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends FosController
{
    public function loginAction(Request $request)
    {
        parent::loginAction($request);
        // Si le visiteur est déjà identifié, on le redirige vers l'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            //On genere l'url de la partie de id table
            $url = $this->generateUrl('jeu_accueil');
            return $this->redirect($url);
        }

        // Le service authentication_utils permet de récupérer le nom d'utilisateur
        // et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
        // (mauvais mot de passe par exemple)
        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('LLUserBundle:Default:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }

    public function logoutAction() {
        //do whatever i want here lol
        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();
        /** Cas ou joueur est dejadans une partie mais veut en creer une nouvelle */
        //Recuperation du user
        $user = $this->getUser();
        //On regarde si il existe deja dans la table joueur
        $joueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('email' => $user->getEmail()));
        $em->remove($joueur);
        $em->flush();
        //clear the token, cancel session and redirect
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        return $this->redirect($this->generateUrl('jeu/accueil'));
    }
}
