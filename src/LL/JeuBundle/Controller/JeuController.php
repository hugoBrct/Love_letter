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

        //incr�mente le nombre de joueur dans la table
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

        //Recuperattion du user
        $user = $this->getUser();

        //Recuperation du joueur courant
        $joueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('email' => $user->getEmail()));

        // On récupère les joueurs a la table
        $listJoueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->recupererListJoueur($table, $joueur);

        //on regarde si il y a plus de 2 joueur
        if(sizeof($listJoueur) > 1){
            //Si oui on change l'etat de la partie
            $table->setEtat("Partie jouable");
            $em->flush();
        }

        return $this->render('JeuBundle:Partie:partie.html.twig',
            array('table' => $table,
                'listCarte' => $listCarte,
                'listJoueur' => $listJoueur,
                'joueur' => $joueur
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
                $pioche = new Pioche();
                $pioche->setTable($table);
                $pioche->setCarte($carte);
                //on persite l'entite
                $em->persist($pioche);
                //on flush
                $em->flush();
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

    public function construireJoueur(ObjectManager $em, $table){
        //Creation d'un joueur
        $joueur = new Joueur();
        $usr= $this->get('security.token_storage')->getToken()->getUser();
        $joueur->setEmail($usr->getEmail());
        $joueur->setTable($table);
        $joueur->setOrdreDePassage($table->getNbJoueur()+1);

        //on persite l'entite
        $em->persist($joueur);
        //on flush
        $em->flush();

        return $joueur;
    }


}
