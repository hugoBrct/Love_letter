<?php

namespace LL\JeuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tableJeu
 *
 * @ORM\Table(name="table_jeu")
 * @ORM\Entity(repositoryClass="LL\JeuBundle\Repository\TableJeuRepository")
 */
class TableJeu
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=50)
     */
    private $etat;

    /**
     * @var int
     *
     * @ORM\Column(name="tour", type="integer")
     */
    private $tour;

    /**
     * @var int
     *
     * @ORM\Column(name="nbJoueur", type="integer")
     */
    private $nbJoueur;

    public function __construct(){
        $this->tour = 0;
        $this->etat = 'en attente d\'autre joueur';
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return tableJeu
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set tour
     *
     * @param integer $tour
     *
     * @return tableJeu
     */
    public function setTour($tour)
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get tour
     *
     * @return int
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * Set nbJoueur
     *
     * @param integer $nbJoueur
     *
     * @return tableJeu
     */
    public function setNbJoueur($nbJoueur)
    {
        $this->nbJoueur = $nbJoueur;

        return $this;
    }

    /**
     * Get nbJoueur
     *
     * @return integer
     */
    public function getNbJoueur()
    {
        return $this->nbJoueur;
    }
}
