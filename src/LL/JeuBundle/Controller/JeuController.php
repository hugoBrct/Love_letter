<?php

namespace LL\JeuBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use LL\JeuBundle\Entity\Joueur;
use LL\JeuBundle\Entity\Pioche;
use LL\JeuBundle\Entity\TableJeu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\FormType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            $this->verifierJoueurPartie();

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

    public function afficherPartieAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        //Recuperation de la table selon l'id en parametre
        $table = $em
            ->getRepository('JeuBundle:TableJeu')
            ->find($id);

        if ($table == null) {
            $this->addFlash('warning', "Un joueur s'est déconnecté");
            //Cas partie supprimé
            $url = $this->generateUrl('jeu_accueil');
            return $this->redirect($url);
        } else {


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
            $tablecarte = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $id, 'etat' => 'faceVisible'));

            //Recuperation des cartes deja jouée
            $cartejouee = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $id, 'etat' => 'jouee'));

            //On affiche un message a l'utilisateur si c'est son tour
            if ($joueur->getOrdreDePassage() == $table->getAQuiLeTour()) {
                $this->addFlash("success", "A toi de jouer!");
            }

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
    }

    public function afficherPartieEffetAction($id, $effet){

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

        //On affiche un message a l'utilisateur si c'est son tour
        if($joueur->getOrdreDePassage() == $table->getAQuiLeTour()){
            $this->addFlash("success", "A toi de jouer!");
        }

        return $this->render('JeuBundle:Partie:partie.html.twig',
            array('table' => $table,
                'listJoueur' => $listJoueur,
                'joueur' => $joueur,
                'carteEnMain' => $carte,
                'carteTable' => $tablecarte,
                'carteJouee' => $cartejouee,
                'effet' => $effet
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
        $joueur->setEtat('joue');

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
        $partie = $em->getRepository('JeuBundle:TableJeu')->find($id);
        $nbJoueur = $partie->getNbJoueur();

        if($nbJoueur >=2){

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
        }else{
            //On genere l'url de la partie de id table
            $url = $this->generateUrl('jeu_partie', array('id' => $id));
            return $this->redirect($url);
        }
    }

    public function lancerManche($idTable){
        $em = $this->getDoctrine()->getManager();

        //On incremente la manche
        $table = $em->getRepository('JeuBundle:TableJeu')->find($idTable);
        $table->setManche($table->getManche() + 1);
        $table->setAQuiLeTour(1);

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
                $joueur->setEtat('joue');
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

        //Si le joueur qu'on a choisit est éliminé , on avance le tour de la table
        while($joueur->getEtat() == 'elimine'){
            if($table->getNbJoueur() == ($table->getAQuiLeTour())){
                $table->setAQuiLeTour('1');
            }
            else{
                $table->setAQuiLeTour($table->getAQuiLeTour() + 1);
            }
            $joueur = $em
                ->getRepository('JeuBundle:Joueur')
                ->findOneBy(array('table' => $idTable, 'ordreDePassage' => $table->getAQuiLeTour()));
        }

        //On enlève son immunité
        if($joueur->getEtat() == 'immunise')
            $joueur->setEtat('joue');


        //Si il ne reste plus assez de joueur, on déclanche une fin de manche
        $listJoueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findBy(array('etat' => array('joue','immunise')));

        if(count($listJoueur)<2){
            $this->finDeManche($idTable);
        }

        //On récupère les cartes encore disponibles
        $listCarte = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $idTable, 'etat' => 'pioche'));
        //Si il ne reste plus de carte, alors on déclanche une fin de manche
        if(!$listCarte) {
            $this->finDeManche($idTable);
        }else{
            //Sinon on continu de distribuer
            $cartePiochee = array_rand($listCarte);
            $carte = $listCarte[$cartePiochee];
            $carte->setEtat('enMain');
            $carte->setProprietaire($joueur);
            $em->flush();
        }
    }

    public function finDeManche($idTable){
        $em = $this->getDoctrine()->getManager();
        //On recupère le propriétaire de la carte la plus forte
        $array = $em->getRepository('JeuBundle:Pioche')->recupererCartePlusForte($idTable);
        foreach($array["gagnant"] as $gagnant){
            $gagnant->setScore($gagnant->getScore()+1);
        }
        //On regarde si on a un vainqueur
        //On change de manche
        $this->lancerManche($idTable);
    }

    public function jouerTourAction($id_table, $id_carte){
        $em = $this->getDoctrine()->getManager();

        $carte = $em
            ->getRepository('JeuBundle:Pioche')
            ->findOneBy(array('id' => $id_carte));
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
        if($table->getAQuiLeTour() != $joueur->getOrdreDePassage()) {
            //C'est pas a nous de jouer
            $url = $this->generateUrl('jeu_partie', array('id' => $id_table));
            return $this->redirect($url);
        }

        //On s'assure qu'il a bien la carte qu'il essaie de jouer
        $carte = $em->getRepository('JeuBundle:Pioche')->findOneBy(array('proprietaire' => $joueur, 'carte' => $carte->getCarte()));
        if($carte == null){
            //La carte qu'on essaie de jouer n'est pas en notre possession
            $url = $this->generateUrl('jeu_partie', array('id' => $id_table));
            return $this->redirect($url);
        }

        //On dépose sa carte sur la table
        $carte = $em->getRepository('JeuBundle:Pioche')->findOneBy(array('proprietaire' => $joueur, 'carte' => $carte->getCarte()));
        $carte->setEtat('jouee');

        //on flush
        $em->flush();

        $em = $this->getDoctrine()->getManager();
        $carte = $em->getRepository('JeuBundle:Cartes')->find($carte->getCarte());

        //Si il n'y a aucune cible possible pour l'effet de la carte, on joue simplement la carte TODO


        //Si la carte n'as pas d'effet qui demande d'information a l'utilisateur, on reload simplement la page
        if($carte->getNom() == "servante" OR $carte->getNom() == "comtesse" OR $carte->getNom() == "princesse"){
            //Si la carte est la servante, on applique l'effet
            if($carte->getNom() == "servante")
                $this->effetServante($id_table,$joueur);
            //Si tout se passe bien, on fait passer le tour et on affiche
            if($table->getNbJoueur() == ($table->getAQuiLeTour())){
                $table->setAQuiLeTour('1');
            }
            else{
                $table->setAQuiLeTour($table->getAQuiLeTour() + 1);
            }

            //on flush
            $em->flush();

            //On lance le tour
            $this->lancerTour($id_table);

            //On genere l'url de la partie de id table
            $url = $this->generateUrl('jeu_partie', array('id' => $id_table));
            return $this->redirect($url);
        }
        //Sinon on applique l'effet
        $url = $this->generateUrl('jeu_effet', array('id' => $id_table, 'effet' => $carte->getNom()));
        return $this->redirect($url);
    }

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
            $this->supprimerPartie($joueur);
        }
    }

    public function supprimerPartie($joueur){
        //Recup l'entity manager
        $em = $this->getDoctrine()->getManager();

        //Si il est dans une partie on supprime la partie (mais sinon non...)
        if($joueur) {
            //on recupere la table
            $table = $joueur->getTable();

            //on recupere les joueurs
            $joueurs = $em
                ->getRepository('JeuBundle:Joueur')
                ->recupererListJoueur($table, $joueur);

            //on recupere la pioche
            $pioche = $em
                ->getRepository('JeuBundle:Pioche')
                ->findBy(array('table' => $table));

            $em->remove($joueur);
            $em->remove($table);
            foreach ($joueurs as $j) {
                $em->remove($j);
            }
            foreach ($pioche as $c) {
                $em->remove($c);
            }
        }
    }

    public function appliquerEffetAction(Request $request){

        $id_table = $request->request->get('id_table');
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $joueur = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('email' => $user->getEmail()));

        //Recuperation de la table selon l'id en parametre
        $table = $em
            ->getRepository('JeuBundle:TableJeu')
            ->find($id_table);

        //En fonction de la carte, on applique different effet
        switch( $request->request->get('carte') ){
            case  'garde':
                //On récupère la cible
                $cible = $request->request->get('cible');
                //On récupère la carte deviné
                $devine = $request->request->get('devinecarte');
                //Appel de la méthode de vérification
                $this->effetGarde($id_table, $joueur, $cible, $devine);
                break;
            case 'pretre':
                //On récupère la cible
                $cible = $request->request->get('cible');
                $this->effetPretre($id_table, $joueur, $cible);
                break;
            case 'baron':
                //On récupère la cible
                $cible = $request->request->get('cible');
                $this->effetBaron($id_table, $joueur, $cible);
                break;
            case 'prince':
                //On récupère la cible
                $cible = $request->request->get('cible');
                $this->effetPrince($id_table, $joueur, $cible);
                break;
            case 'roi':
                //On récupère la cible
                $cible = $request->request->get('cible');
                $this->effetRoi($id_table, $joueur, $cible);
                break;
            default:
                //Rien a faire pour la princesse et la comtesse
                break;
        }


        //Si tout se passe bien, on fait passer le tour et on affiche
        if($table->getNbJoueur() == ($table->getAQuiLeTour())){
            $table->setAQuiLeTour('1');
        }
        else{
            $table->setAQuiLeTour($table->getAQuiLeTour() + 1);
        }

        //on flush
        $em->flush();

        //On lance le tour
        $this->lancerTour($id_table);

        //On genere l'url de la partie de id table
        $url = $this->generateUrl('jeu_partie', array('id' => $id_table));
        return $this->redirect($url);
    }

    public function effetGarde($id_table, $joueur, $cible, $devine){
        $em = $this->getDoctrine()->getManager();
        //On récupère la carte de la cible
        $adversaire = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('id' => $cible));
        $carteCible = $em
            ->getRepository('JeuBundle:Pioche')
            ->findOneBy(array('proprietaire' => $cible));
        //Si c'est la même que celle donnée par le joueur actuel
        if($carteCible->getCarte()->getNom() == $devine){
            //On dépose la carte
            $carteCible->setEtat('jouee');
            //On change l'état de l'utilisateur a éliminé
            $adversaire->setEtat('elimine');
        }
        //Sinon il se passe rien...

    }

    public function effetPretre($id_table, $joueur, $cible){
        //On affiche un message avec une image dedans ??
    }

    public function effetServante($id_table, $joueur){
        //On change l'etat du joueur a "immunisé"
        $joueur->setEtat('immunise');
    }

    public function effetBaron($id_table, $joueur, $cible){
        $em = $this->getDoctrine()->getManager();
        //On récupère la carte de la cible
        $adversaire = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('id' => $cible));
        $carteCible = $em
            ->getRepository('JeuBundle:Pioche')
            ->findOneBy(array('proprietaire' => $cible));
        //On récupère la carte du joueur
        $carteJoueur = $em
            ->getRepository('JeuBundle:Pioche')
            ->findOneBy(array('proprietaire' => $joueur->getId()));
        //On compare les deux
        if($carteCible->getCarte()->getPoint() >= $carteJoueur->getCarte()->getPoint()){
            $carteJoueur->setEtat('jouee');
            $joueur->setEtat('elimine');
        }else{
            $carteCible->setEtat('jouee');
            $adversaire->setEtat('elimine');
        }

    }

    public function effetPrince($id_table, $joueur, $cible){
        $em = $this->getDoctrine()->getManager();
        $adversaire = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('id' => $cible));
        //Si il reste encore une carte dans le deck
        $listCarte = $em->getRepository('JeuBundle:Pioche')->findBy(array('table' => $id_table, 'etat' => 'pioche'));
        if($listCarte) {
            //On dépose la carte actuellement en main
            $carteCible = $em
                ->getRepository('JeuBundle:Pioche')
                ->findOneBy(array('proprietaire' => $cible));
            $carteCible->setEtat('jouee');
            //On reprend une autre carte
            //Sinon on continu de distribuer
            $cartePiochee = array_rand($listCarte);
            $carte = $listCarte[$cartePiochee];
            $carte->setEtat('enMain');
            $carte->setProprietaire($adversaire);
            $em->flush();
        }


    }

    public function effetRoi($id_table, $joueur, $cible){
        $em = $this->getDoctrine()->getManager();
        //On récupère la carte de la cible
        $adversaire = $em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('id' => $cible));
        $carteCible = $em
            ->getRepository('JeuBundle:Pioche')
            ->findOneBy(array('proprietaire' => $cible));
        //On récupère la carte du joueur
        $carteJoueur = $em
            ->getRepository('JeuBundle:Pioche')
            ->findOneBy(array('proprietaire' => $joueur->getId()));
        //On échange!
        $carteCible->setProprietaire($joueur);
        $carteJoueur->setProprietaire($adversaire);

    }

}
