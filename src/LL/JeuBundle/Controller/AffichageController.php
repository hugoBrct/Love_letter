<?php

namespace LL\JeuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AffichageController extends Controller
{
    public function accueilAction(Request $req)
    {
        $form = $this->get('form.factory')->createBuilder(FormType::class)
            ->setMethod('POST')
            ->add('idtable', TextType::class, array('required' => false) )
            ->add('submit',      SubmitType::class)
            ->getForm();


        // Si la requête est en POST
        if ($req->isMethod('POST')) {
            $form->handleRequest($req);

            if ($form->isValid()) {
                $id = $form->get('idtable')->getData();
                if ($id == null) {
                    $id = 0;
                }
                return $this->redirectToRoute('jeu_rejoindrePartie', array('id' => $id));
            }
        }

        return $this->render('JeuBundle:Default:index.html.twig', array('form' => $form->createView()));
    }
}
