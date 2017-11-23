<?php

namespace LL\JeuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JeuController extends Controller
{
    public function partieAction()
    {
        return $this->render('JeuBundle:Partie:partie.html.twig');
    }
}
