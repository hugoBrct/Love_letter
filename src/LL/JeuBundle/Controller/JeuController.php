<?php

namespace LL\JeuBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use LL\JeuBundle\Entity\Pioche;
use LL\JeuBundle\Entity\TableJeu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JeuController extends Controller
{
    //hashmap pour la creation du deck
    private $tabDeck = array(
    "Princesse" => "1",
    "Comptesse" => "1",
    "Roi" => "1",
    "Prince" => "2",
    "Servante" => "2",
    "Baron" => "2",
    "Pretre" => "2",
    "Garde" => "5",
    );

    public function creerPartieAction() {
        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();

        //construction de la table
        $table = $this->construireTable($em);

        //Construction de la pioche
        $this->construirePioche($em, $table);

        //Recuperation des cartes appartenant a la table
        $listCarte = $em
            ->getRepository('JeuBundle:Pioche')
            ->findBy(array('table' => $table));

        return $this->render('JeuBundle:Partie:partie.html.twig', array('id' => $table->getId(), 'etat' => $table->getEtat(), 'listCarte' => $listCarte));

    }

    public function construirePioche(ObjectManager $em, $table){
        //On recupere les cartes
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('JeuBundle:Cartes');

        $listCartes = $repository->findAll();


        //Creation du jeu de carte
        foreach($listCartes as $carte){
            $compteur = 0;
            while($compteur < $this->tabDeck[$carte->getNom()]){
                $pioche = new Pioche();
                $pioche->setTable($table);
                $pioche->setCarte($carte);
                //on persite l'entite
                $em->persist($pioche);
                //on flush
                $em->flush();

                $compteur++;
            }
        }
    }

    public function construireTable(ObjectManager $em){
        //Creation de l'entit� table
        $table = new TableJeu();
        $table->setNbJoueur(1);
        //le reste est d�fini automatiquement dans le constructeur

        //on persite l'entite
        $em->persist($table);
        //on flush
        $em->flush();

        return $table;
    }


}
