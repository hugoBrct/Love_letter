<?php

namespace LL\JeuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Joueur
 *
 * @ORM\Table(name="joueur")
 * @ORM\Entity(repositoryClass="LL\JeuBundle\Repository\JoueurRepository")
 */
class Joueur
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;


    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="LL\JeuBundle\Entity\TableJeu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $table;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255)
     */
    private $etat;






    /**
     * @var int
     *
     * @ORM\Column(name="ordreDePassage", type="integer")
     */
    private $ordreDePassage;

    /**
     * Constructeur par defaut d'un joueur
     */
    public function __construct(){
        $this->score = 0;
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
     * Set email
     *
     * @param string $email
     *
     * @return Joueur
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Joueur
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set ordreDePassage
     *
     * @param integer $ordreDePassage
     *
     * @return Joueur
     */
    public function setOrdreDePassage($ordreDePassage)
    {
        $this->ordreDePassage = $ordreDePassage;

        return $this;
    }

    /**
     * Get ordreDePassage
     *
     * @return integer
     */
    public function getOrdreDePassage()
    {
        return $this->ordreDePassage;
    }

    /**
     * Set table
     *
     * @param \LL\JeuBundle\Entity\TableJeu $table
     *
     * @return Joueur
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
     * @return string
     */
    public function getIdString(){
        return "$this->id";
    }

    /**
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param string $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

}
