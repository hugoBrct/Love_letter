<?php

namespace LL\JeuBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use LL\JeuBundle\Entity\Joueur;
use LL\JeuBundle\Entity\Pioche;
use LL\JeuBundle\Entity\TableJeu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        //Construction d'un joueur
        $joueur = $this->construireJoueur($em, $table);


        //On genere l'url de la partie de id table
        $url = $this->generateUrl('jeu_partie', array('id' => $table->getId()));
        return $this->redirect($url);
    }

    public function rejoindrePartieAction(){
        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();
        //Recuperation d'une table non pleine
        $table = $em
            ->getRepository('JeuBundle:TableJeu')
            ->trouverPartie();

        //On creer un joueur
        $this->construireJoueur($em, $table);

        //incrémente le nombre de joueur dans la table
        $table->setNbJoueur($table->getNbJoueur()+1);
        $em->flush();

        //On genere l'url de la partie de id table
        $url = $this->generateUrl('jeu_partie', array('id' => $table->getId()));
        return $this->redirect($url);
    }

    public function afficherPartieAction($id){
        $em = $this->getDoctrine()->getManager();

        //Recuperation de la table selon l'id en parametre
        $table = $em
            ->getRepository('JeuBundle:TableJeu')
            ->find($id);

        //Recuperation des cartes appartenant a la table
        $listCarte = $em
            ->getRepository('JeuBundle:Pioche')
            ->findBy(array('table' => $table));

        // On récupère les joueurs a la table
        $listJoueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findBy(array('table' => $table));

        //on regarde si il y a plus de 2 joueur
        if(sizeof($listJoueur) >= 2){
            //Si oui on change l'etat de la partie
            $table->setEtat("Partie jouable");
            $em->flush();
        }

        return $this->render('JeuBundle:Partie:partie.html.twig',
            array('table' => $table,
                'listCarte' => $listCarte,
                'joueur' => $listJoueur,
            )
        );

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
        //Creation de l'entité table
        $table = new TableJeu();
        $table->setNbJoueur(1);
        //le reste est défini automatiquement dans le constructeur

        //on persite l'entite
        $em->persist($table);
        //on flush
        $em->flush();

        return $table;
    }

    public function construireJoueur(ObjectManager $em, $table){
        //Creation d'un joueur
        $joueur = new Joueur();
        $joueur->setEmail("test". rand(0, 100)."@test.fr");
        $joueur->setTable($table);
        $joueur->setOrdreDePassage($table->getNbJoueur()+1);

        //on persite l'entite
        $em->persist($joueur);
        //on flush
        $em->flush();

        return $joueur;
    }


}
