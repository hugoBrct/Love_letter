<?php

namespace LL\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //On regarde si l'utilisateur est deja connecté ou non
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //Si il est deja connecté, on le redirige vers l'acceuil du jeu
            return $this->render('JeuBundle:Default:index.html.twig');
        }


        //On propose a l'utilisateur de se connecter ou de creer un compte.

        return $this->render('LLUserBundle:Default:login.html.twig', array(
            'last_username' => '',
            'error'         => null,
        ));
    }
}
