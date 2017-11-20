<?php

namespace LL\JeuBundle\Entity;

/**
 * Created by PhpStorm.
 * User: Brécourt
 * Date: 20/11/2017
 * Time: 14:29
 */
class Joueur {

    protected $email;

    protected $id_table;

    protected $point;

    // getters/setters
    public function getMail(){
        return $this->email;
    }

    public function getIdTable(){
        return $this->id_table;
    }

    public function getPoint(){
        return ;
    }

}