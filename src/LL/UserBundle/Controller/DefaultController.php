<?php

namespace LL\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LLUserBundle:Default:login.html.twig');
    }
}
