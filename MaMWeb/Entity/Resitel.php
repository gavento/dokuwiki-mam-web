<?php

namespace MaMWeb\Entity;

/**
 * @Entity @Table(name="resitele")
 */
class Resitel {
    /**
     * Interni MaM id
     * 
     * @Id @Column(type="integer") @GeneratedValue
     **/
    public $id;

    /**
     * @Column(type="string", nullable=false)
     **/
    public $jmeno;

    /**
     * @Column(type="string", nullable=false)
     **/
    public $prijmeni;

    /**
     * Username, pokud ma wiki-ucet.
     *
     * @Column(type="string", unique=true)
     **/
    public $username;

    /**
     * Pohlaví, že ho neznáme se snad nestane (a ušetří to práci při programování) 
     *
     * @Column(type="boolean", nullable=false)
     **/
    public $pohlavi_muz;

    /**
     * Škola
     *
     * @ManyToOne(targetEntity="Skola", inversedBy="resitele")
     * @JoinColumn(name="skola", referencedColumnName="id")
     **/
    public $skola;

    /** 
     * Očekávaný rok maturity
     *
     * @Column(type="integer", nullable=false)
     **/
    public $rok_maturity;

    /** Kontakty a detaily, pokud známe **/

    /**
     * @Column(type="string")
     **/
    public $email;

    /**
     * @Column(type="string")
     **/
    public $telefon;

    /**
     * @Column(type="date")
     **/
    public $datum_narozeni;

    /** Souhlasy - NULL dokud nedali souhlas **/

    /**
     * Souhlas se zpracováním osobních údajů
     *
     * @Column(type="date")
     **/
    public $datum_souhlasu;

    /**
     * Souhlas se zasíláním fakultních informací.
     *
     * @Column(type="date")
     **/
    public $datum_souhlasu_spam;

    /**
     * Datul připojení řešitele k MaM
     *
     * @Column(type="date", nullable=false)
     **/
    public $datum_vytvoreni;

    /**
     * Kam zasílat papírové řešení
     *
     **/
    // kam_posilat kam_posilat_enum NOT NULL, TODO:enum

    /** Adresa, pokud ji známe. Ulice může být jen číslo **/

    /** @Column(type="string", nullable=true) **/
    public $ulice;
    /** @Column(type="string", nullable=true) **/
    public $mesto;
    /** @Column(type="string", nullable=true) **/
    public $psc;

    /**
     * ISO 3166-1 dvojznakovy kod zeme velkym pismem (CZ, SK)
     * 
     * @Column(type="string", nullable=true)
     **/
    public $stat;



    public function __construct() {
	$this->datum_vytvoreni = new DateTime("now");
	$this->zadane_problemy = new \Doctrine\Common\Collections\ArrayCollection();
	$this->resene_problemy = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


