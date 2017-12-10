<?php

namespace LL\JeuBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use LL\JeuBundle\Entity\Joueur;
use LL\JeuBundle\Entity\Pioche;
use LL\JeuBundle\Entity\TableJeu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JeuController extends Controller
{

    public function creerPartieAction() {
        //Verif user pas deja dans une partie
        $this->verifierJoueurPartie();

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

    public function rejoindrePartieAction($id){
        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();

        $table  = null;
        if($id == 0) {
            //Recuperation d'une table non pleine
            $table = $em
                ->getRepository('JeuBundle:TableJeu')
                ->trouverPartie();
        }else{
            $table = $em
                ->getRepository('JeuBundle:TableJeu')
                ->find($id);
        }
        if($table == null) {
            // flash msg
            $this->get('session')->getFlashBag()->add('error', "La table $id n'existe pas");
            // some redirection e. g. to referer
            return $this->redirectToRoute('jeu_accueil');
        }else {

            //incr�mente le nombre de joueur dans la table
            $table->setNbJoueur($table->getNbJoueur() + 1);

            //On creer un joueur
            $this->construireJoueur($em, $table);

            $em->flush();

            //On genere l'url de la partie de id table
            $url = $this->generateUrl('jeu_partie', array('id' => $table->getId()));
            return $this->redirect($url);
        }
    }



    public function afficherPartieAction($id){

        $em = $this->getDoctrine()->getManager();

        //Recuperation de la table selon l'id en parametre
        $table = $em
            ->getRepository('JeuBundle:TableJeu')
            ->find($id);


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

        //Recuperation de la ou les cartes du joueur courant
        $carte = $em->getRepository('JeuBundle:Pioche')->findBy(array('proprietaire' => $joueur, 'etat' => 'enMain'));

        //Recuperation des cartes sur la table
        $tablecarte = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $id , 'etat' => 'faceVisible'));

        //Recuperation des cartes deja jouée
        $cartejouee = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $id , 'etat' => 'jouee'));


        return $this->render('JeuBundle:Partie:partie.html.twig',
            array('table' => $table,
                'listJoueur' => $listJoueur,
                'joueur' => $joueur,
                'carteEnMain' => $carte,
                'carteTable' => $tablecarte,
                'carteJouee' => $cartejouee
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
        $joueur->setOrdreDePassage($table->getNbJoueur());
        $joueur->setTable($table);

        //on persite l'entite
        $em->persist($joueur);
        //on flush
        $em->flush();

        return $joueur;
    }

    public function refreshPartieAction(Request $req){
        if($req->isXmlHttpRequest()){
            return new JsonResponse("Victoire",200);
        }
        return new JsonResponse("Echec", 400);
    }

    public function lancerPartieAction($id){

        $em = $this->getDoctrine()->getManager();

        //On s'assure qu'au moins deux joueurs sont présents TODO
        //On change l'etat de la partie
        $partie = $em->getRepository('JeuBundle:TableJeu')->find($id);
        $partie->setEtat("Ferme");

        $em->flush();

        //On lance la manche
        $this->lancerManche($id);

        //On genere l'url de la partie de id table
        $url = $this->generateUrl('jeu_partie', array('id' => $id));
        return $this->redirect($url);
    }

    /**
     * Permet de lancer une manche
     * @param $idTable, id de la table
     */
    public function lancerManche($idTable){
        $em = $this->getDoctrine()->getManager();

        //reinitialisation de toutes les cartes dans la pioche
        $listCarte = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $idTable));

        foreach($listCarte as $carte){
            $carte->setEtat('pioche');
            $carte->setProprietaire(null);
        }

        $listJoueur = $em->getRepository('JeuBundle:Joueur')->findBy(array('table' => $idTable));

        //Recuperation de x cartes
        //et distribution de 1 carte aux joueurs
        if(sizeof($listJoueur == 2)){
            /**  Cas 2 joueurs  **/
            $x = sizeof($listJoueur) + 4;
            $cartePiochey = array_rand($listCarte, $x);

            //Ecarter 1 carte face cachée
            $carte = $listCarte[$cartePiochey[0]];
            $carte->setEtat('ecarte');

            // 3 cartes face visible
            for($i = 1; $i < 4; $i++){
                $carte = $listCarte[$cartePiochey[$i]];
                $carte->setEtat('faceVisible');
            }
            //Attribution de 1 carte pour chaque joueur
            $i = 4;
            foreach($listJoueur as $joueur) {
                $carte = $listCarte[$cartePiochey[$i]];
                $carte->setEtat('enMain');
                $carte->setProprietaire($joueur);
                $i++;
            }
        }else{
            /**  Cas plus de 2 joueurs  **/
            $x = sizeof($listJoueur) + 1;
            $cartePiochey = array_rand($listCarte, $x);

            //Ecarter 1 carte face cachée
            $carte = $listCarte[$cartePiochey[0]];
            $carte->setEtat('ecarte');

            //Attribution de 1 carte pour chaque joueur
            $i = 1;
            foreach($listJoueur as $joueur) {
                $carte = $listCarte[$cartePiochey[$i]];
                $carte->setEtat('enMain');
                $carte->setProprietaire($joueur);
                $i++;
            }
        }
        $em->flush();

        //On lance le tour
        $this->lancerTour($idTable);
    }

    public function lancerTour($idTable){

        $em = $this->getDoctrine()->getManager();
        //On regarde qui est le joueur de ce tour
        $table = $em
            ->getRepository('JeuBundle:TableJeu')
            ->find($idTable);

        $joueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('table' => $idTable, 'ordreDePassage' => $table->getAQuiLeTour()));

        //On lui distribu une carte
        $listCarte = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $idTable, 'etat' => 'pioche'));

        //Si il ne reste plus de carte, alors on déclanche une fin de manche
        if(!$listCarte) {
            //On recupère le propriétaire de la carte la plus forte
            $array = $em->getRepository('JeuBundle:Pioche')->recupererCartePlusForte($idTable);
            foreach($array["gagnant"] as $gagnant){
                $gagnant->setScore($array["points"]);
            }
            //On change de manche
            $this->lancerManche($idTable);

        }else{
            //Sinon on continu de distribuer
            $cartePiochee = array_rand($listCarte);
            $carte = $listCarte[$cartePiochee];
            $carte->setEtat('enMain');
            $carte->setProprietaire($joueur);
            $em->flush();
        }
    }

    public function jouerTourAction($id_table, $id_carte){
        $em = $this->getDoctrine()->getManager();
        //Recuperation de la table selon l'id en parametre
        $table = $em
            ->getRepository('JeuBundle:TableJeu')
            ->find($id_table);
        //Recuperation du joueur en fonction de l'id de l'utilisateur
        $user = $this->getUser();
        $joueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('email' => $user->getEmail()));

        //On vérifie que c'était bien au tour de l'utilisateur de jouer
        if($table->getAQuiLeTour() != ($joueur->getOrdreDePassage())) {
            //C'est pas a nous de jouer
            $url = $this->generateUrl('jeu_partie', array('id' => $id_table));
            return $this->redirect($url);
        }
        //On s'assure qu'il a bien la carte qu'il essaie de jouer
        $carte = $em->getRepository('JeuBundle:Pioche')->findOneBy(array('proprietaire' => $joueur, 'carte' => $id_carte));
        if($carte == null){
            //La carte qu'on essaie de jouer n'est pas en notre possession
            $url = $this->generateUrl('jeu_partie', array('id' => $id_table));
            return $this->redirect($url);
        }
        //On dépose sa carte sur la table
        $carte->setEtat('jouee');
        //On fait passer le tour au suivant (ou a la manche suivante au cas ou)

        if($table->getNbJoueur() == ($table->getAQuiLeTour())){
            $table->setAQuiLeTour('1');
        }
        else{
            $table->setAQuiLeTour($table->getAQuiLeTour() + 1);
        }
        //on flush
        $em->flush();

        $this->lancerTour($id_table);

        //On genere l'url de la partie de id table
        $url = $this->generateUrl('jeu_partie', array('id' => $id_table));
        return $this->redirect($url);
    }
    /**
     * Verifie qu'un utilisateur n'est pas deja joueur dans une partie
     * si oui on supprime le joueur de l'ancienne partie
     */
    public function verifierJoueurPartie(){
        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();
        /** Cas ou joueur est dejadans une partie mais veut en creer une nouvelle */
        //Recuperation du user
        $user = $this->getUser();
        //On regarde si il existe deja dans la table joueur
        $joueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('email' => $user->getEmail()));
        if($joueur != null){
            $tabJoueur = array($joueur);
            $this->supprimerJoueurPartie($tabJoueur);
        }
    }

    /**
     * Supprime les joueurs en parametre
     * @param $tabJoueur, tableau de joueur
     */
    public function supprimerJoueurPartie($tabJoueur){
        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();
        foreach($tabJoueur as $joueur){
            $em->remove($joueur);
            $em->flush();
        }
    }

}
