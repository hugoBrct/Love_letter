<?php

namespace LL\UserBundle\Listeners;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManager;
/**
 * Created by PhpStorm.
 * User: Brécourt
 * Date: 10/12/2017
 * Time: 15:35
 */
class LogoutListener implements LogoutHandlerInterface {

    protected $containerInterface;
    protected $em;

    public function __construct(ContainerInterface $container, EntityManager $manager){
        $this->containerInterface = $container;
        $this->em = $manager;
    }

    public function logout(Request $Request, Response $Response, TokenInterface $Token) {
        /** Cas ou joueur est dejadans une partie mais veut en creer une nouvelle */
        //Recuperation du user
        $user = $this->containerInterface->get('security.token_storage')->getToken()->getUser();

        //On recuperer le joueur
        $joueur = $this->em
            ->getRepository('JeuBundle:Joueur')
            ->findOneBy(array('email' => $user->getEmail()));

        //on recupere la table
        $table = $joueur->getTable();

        //on recupere les joueurs
        $joueurs = $this->em
            ->getRepository('JeuBundle:Joueur')
            ->recupererListJoueur($table, $joueur);

        //on recupere la pioche
        $pioche = $this->em
            ->getRepository('JeuBundle:Pioche')
            ->findBy(array('table' => $table));

        $this->em->remove($joueur);
        $this->em->remove($table);
        foreach($joueurs as $j){
            $this->em->remove($j);
        }
        foreach($pioche as $c){
            $this->em->remove($c);
        }


        $this->em->flush();
    }

}