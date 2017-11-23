<?php

namespace LL\JeuBundle\Controller;


use LL\JeuBundle\Entity\TableJeu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TableJeuController extends Controller
{
    public function creeTableAction() {
        //Creation de l'entit�
        $table = new TableJeu();
        $table->setNbJoueur(1);
        //le reste est d�fini automatiquement dans le constructeur

        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();
        //on persite l'entite
        $em->persist($table);
        //on flush
        $em->flush();

        return $this->render('JeuBundle:Partie:partie.html.twig', array('id' => $table->getId(), 'etat' => $table->getEtat()));
    }
}
