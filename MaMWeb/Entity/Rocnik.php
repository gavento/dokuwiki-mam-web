<?php

namespace MaMWeb\Entity;

/**
 * @Entity @Table(name="rocniky")
 */
class Rocnik {
    /**
     * Interní id
     *
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;

    /**
     * Číslo ročníku 1..21..
     * 
     * @Column(type="integer", nullable=false, unique=true)
     **/
    private $rocnik;
    public function get_rocnik() { return $this->rocnik; }
    public function set_rocnik($rocnik) { $this->rocnik = $rocnik; }

    /**
     * Stránka ročníku na wiki
     *
     * @Column(type="string", nullable=false, unique=true)
     **/
    private $pageid;
    public function get_pageid() { return $this->pageid; }
    public function set_pageid($pageid) { $this->pageid = $pageid; }

    /**
     * Rok zacatku rocniku. Tedy rocnik 2013/2014 bude mit rok "2013"
     *
     * @Column(type="integer", nullable=false, unique=true)
     **/
    private $prvni_rok;
    public function get_prvni_rok() { return $this->prvni_rok; }
    public function set_prvni_rok($prvni_rok) { $this->prvni_rok = $prvni_rok; }

    /**
     * Pole cisel rocniku
     * TOTO POLE SE NESMI PŘÍMO MODIFIKOVAT (nastavte položky obsažených entit)
     * 
     * @OneToMany(targetEntity="Cislo", mappedBy="rocnik")
     **/
    private $cisla;
    public function get_cisla() { return $this->cisla; }

    /**
     * Pole soustredeni rocniku
     * TOTO POLE SE NESMI PŘÍMO MODIFIKOVAT (nastavte položky obsažených entit)
     * 
     * @OneToMany(targetEntity="Soustredeni", mappedBy="rocnik")
     **/
    private $soustredeni;
    public function get_soustredeni() { return $this->soustredeni; }

    /**
     * Vrati rocnik ve tvaru "2013/2014"
     */
    public function get_roky() {
        return $this->get_prvni_rok() . "/" . ($this->get_prvni_rok() + 1);
    }

    public function __construct($rocnik, $pageid) {
	$this->set_rocnik($rocnik);
	$this->set_prvni_rok($rocnik + 1993);
	$this->set_pageid($pageid);
	$this->cisla = new \Doctrine\Common\Collections\ArrayCollection();
	$this->soustredeni = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


