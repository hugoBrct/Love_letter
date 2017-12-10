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
     * @ORM\Column(name="aQuiLeTour", type="integer")
     */
    private $aQuiLeTour;

    /**
     * @var int
     *
     * @ORM\Column(name="manche", type="integer")
     */
    private $manche;

    /**
     * @var int
     *
     * @ORM\Column(name="nbJoueur", type="integer")
     */
    private $nbJoueur;




    public function __construct(){
        $this->tour = 0;
        $this->etat = 'en attente d\'autre joueur';
        $this->aQuiLeTour = 1;
        $this->manche = 0;
        $this->nbJoueur = 0;
    }




    /**
     * Get id
     *
     * @return integer
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
     * @return TableJeu
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
     * Set aQuiLeTour
     *
     * @param integer $aQuiLeTour
     *
     * @return TableJeu
     */
    public function setAQuiLeTour($aQuiLeTour)
    {
        $this->aQuiLeTour = $aQuiLeTour;

        return $this;
    }

    /**
     * Get aQuiLeTour
     *
     * @return integer
     */
    public function getAQuiLeTour()
    {
        return $this->aQuiLeTour;
    }

    /**
     * Set manche
     *
     * @param integer $manche
     *
     * @return TableJeu
     */
    public function setManche($manche)
    {
        $this->manche = $manche;

        return $this;
    }

    /**
     * Get manche
     *
     * @return integer
     */
    public function getManche()
    {
        return $this->manche;
    }

    /**
     * Set nbJoueur
     *
     * @param integer $nbJoueur
     *
     * @return TableJeu
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
