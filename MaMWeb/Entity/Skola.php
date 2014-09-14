<?php

namespace MaMWeb\Entity;

/**
 * Mělo by být částečně vytaženo z Aesopa, viz https://ovvp.mff.cuni.cz/wiki/aesop/export-skol.
 *
 * @Entity @Table(name="skoly")
 **/
class Skola {
    /**
     * interní id v MaM
     * @Id @Column(type="integer") @GeneratedValue
     **/
    public $id;

    /**
     * aesopi ID
     * @Column(type="string", unique=true)
     **/
    public $aesop_id;

    /**
     * IZO školy (jen české školy)
     *
     * @Column(type="string")
     **/
    public $izo;

    /**
     * Celý název školy
     *
     * @Column(type="string", nullable=false)
     **/
    public $nazev;

    /**
     * Zkraceny nazev pro zobrazení ve výsledkovce, volitelné.
     * Není v Aesopovi, musíme vytvářet sami.
     *
     * @Column(type="string")
     **/
    public $kratky_nazev;

    /** Adresa. Ulice může být jen číslo **/

    /** @Column(type="string", nullable=false) **/
    public $ulice;
    /** @Column(type="string", nullable=false) **/
    public $mesto;
    /** @Column(type="string", nullable=false) **/
    public $psc;

    /**
     * ISO 3166-1 dvojznakovy kod zeme velkym pismem (CZ, SK)
     * 
     * @Column(type="string", nullable=false)
     **/
    public $stat;

    /** @OneToMany(targetEntity="Resitel", mappedBy="skola") **/
    public $resitele;


    public function __construct() {
	//$this->datum_vytvoreni=
	$this->resitele = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


