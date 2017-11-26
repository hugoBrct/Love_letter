<?php

namespace LL\JeuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AffichageController extends Controller
{
    public function accueilAction()
    {
        return $this->render('JeuBundle:Default:index.html.twig');
    }
}
