<?php

namespace LL\JeuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pioche
 *
 * @ORM\Table(name="pioche")
 * @ORM\Entity(repositoryClass="LL\JeuBundle\Repository\PiocheRepository")
 */
class Pioche
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
     * @ORM\Column(name="etat", type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="LL\JeuBundle\Entity\TableJeu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $table;

    /**
     * @ORM\ManyToOne(targetEntity="LL\JeuBundle\Entity\Cartes")
     */
    private $carte;

    public function __construct()
    {
        $this->etat = "pioche";
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
     * @return Pioche
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
     * Set table
     *
     * @param \LL\JeuBundle\Entity\TableJeu $table
     *
     * @return Pioche
     */
    public function setTable(\LL\JeuBundle\Entity\TableJeu $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return \LL\JeuBundle\Entity\TableJeu
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set carte
     *
     * @param \LL\JeuBundle\Entity\Cartes $carte
     *
     * @return Pioche
     */
    public function setCarte(\LL\JeuBundle\Entity\Cartes $carte = null)
    {
        $this->carte = $carte;

        return $this;
    }

    /**
     * Get carte
     *
     * @return \LL\JeuBundle\Entity\Cartes
     */
    public function getCarte()
    {
        return $this->carte;
    }
}
