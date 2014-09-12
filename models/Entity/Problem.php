<?php

namespace Entity;

/**
 * @Entity @Table(name="problemy")
 */
class Problem {
    /**
     * @Id
     * @Column(type="integer", nullable=false, unique=true)
     * @GeneratedValue
     **/
    public $id;

    /** @Column(type="string", nullable=false, unique=true) **/
    public $nazev;

    /** @Column(type="string", nullable=false, unique=true) **/
    public $pageid;

    /** @Column(type="string") **/
    public $verejne_pageid;

    /** @Column(type="string") **/
    public $typ;

    /** @Column(type="string") **/
    public $zadavatel;

    /** @Column(type="string") **/
    public $opravovatel;

    /** @Column(type="string") **/
    public $stav;

    /** @Column(type="integer") **/
    public $cislo_problemu;

    /** @Column(type="integer") **/
    public $body;

    /**
     * @ManyToOne(targetEntity="Entity\Cislo", inversedBy="zadane_problemy")
     * @JoinColumn(name="prvni_cislo", referencedColumnName="id")
     **/
//    public $prvni_cislo;

    /**
     * @ManyToOne(targetEntity="Entity\Cislo", inversedBy="resene_problemy")
     * @JoinColumn(name="reseni_cislo", referencedColumnName="id")
     **/
//    public $reseni_cislo;

    /** @Column(type="date") **/
    public $datum_vytvoreni;

    /**
     * @ManyToMany(targetEntity="Entity\Tag", inversedBy="problemy")
     * @JoinTable(name="problemy_tagy")
     **/
    public $tagy;

    public function __construct() {
	//$this->datum_vytvoreni=
	$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }




    public function getKod() {
    	if ($this->cislo_problemu === null) return null;
	if ($this->typ == 'uloha') {
	    $cislo = '?'; // TODO cislo cisla
            return "u{$cislo}.{$this->cislo_problemu}";
        }
	return "t{$this->cislo_problemu}";
    }

    public $nazvyTypu = array(
	'uloha' => 'Úloha',
	'tema' => 'Téma',
	'serial' => 'Seriál',
	'org-clanek' => 'Článek',
	'res-clanek' => 'Článek',
    );

    public function getNazevTypu() {
	return $this->nazvyTypu[$this->typ];
    }

}


