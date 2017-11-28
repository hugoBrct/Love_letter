<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        //On regarde si l'utilisateur est deja connecté ou non
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //Si il est deja connecté, on le redirige vers l'acceuil du jeu
            return $this->render('JeuBundle:Default:index.html.twig');
        }


        //On propose a l'utilisateur de se connecter ou de creer un compte.
        return $this->render('LLUserBundle:Default:homepage.html.twig');
    }
}
