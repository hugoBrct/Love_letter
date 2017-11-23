<?php

namespace LL\JeuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JeuBundle:Default:login.html.twig');
    }
}
