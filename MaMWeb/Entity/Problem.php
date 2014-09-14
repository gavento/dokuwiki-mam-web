<?php

namespace MaMWeb\Entity;

/**
 * Vytváří se pro návrh úlohy, tématu, seriálu, org či řešitelského článku a konfery, i pro už publikovaný obsah.
 *
 * Problém má několik stavů ($stav):
 * 1. Navržený (navrh)      - existuje stránka návrhem a diskusí (typicky v /org/p/*)
 * 2. Zamítnutý (smazany)   - jen pro ještě nezadané problémy
 * 3. Publikovaný (verejny) - definované `verejne_pageid` (typicky v /p/*) a zároveň `cislo_zadani`.
 * 4. Zveřejněné i řešení (verejny) - definované navíc `cislo_reseni` (jen u ulohy)
 *
 * K tomuto záznamu přísluší dvě wiki stránky - jedna s návrhem a diskusí (v /org/p/),
 * druhá pak s veřejným zadáním a později i příspěvky či řešením (v /p/).
 * 
 * @Entity @Table(name="problemy")
 */
class Problem {
    /**
     * interní id
     * 
     * @Id @Column(type="integer") @GeneratedValue
     **/
    public $id;

    /**
     * název bez čísla, např 'Poissonova'
     * 
     * @Column(type="string", nullable=false, unique=true)
     **/
    public $nazev;

    /** 
     * Typ: úloha/téma/...
     * 
     * @Column(type="string", nullable=false) TODO: enum
     **/
    public $typ;

    /**
     * Org-stránka úlohy na wiki
     *
     * @Column(type="string", nullable=false, unique=true)
     **/
    public $pageid;

    /**
     * stav problému @Column(type="string") **/
    public $stav;

    /** @Column(type="string") **/
    public $verejne_pageid;

    /** @Column(type="string") **/
    public $zadavatel;

    /** @Column(type="string") **/
    public $opravovatel;

    /** @Column(type="integer") **/
    public $cislo_problemu;

    /** @Column(type="integer") **/
    public $body;

    /**
     * @ManyToOne(targetEntity="Cislo", inversedBy="zadane_problemy")
     * @JoinColumn(name="cislo_zadani", referencedColumnName="id")
     **/
    public $cislo_zadani;

    /**
     * @ManyToOne(targetEntity="Cislo", inversedBy="resene_problemy")
     * @JoinColumn(name="cislo_reseni", referencedColumnName="id")
     **/
    public $cislo_reseni;

    /** @Column(type="date") **/
    public $datum_vytvoreni;

    /**
     * @ManyToMany(targetEntity="Tag", inversedBy="problemy")
     * @JoinTable(name="problemy_tagy")
     **/
    public $tagy;

    public function __construct($nazev, $typ, $pageid) {
	$this->nazev = $nazev;
	$this->typ = $typ;
	$this->pageid = $pageid;
	$this->datum_vytvoreni = DateTime("now");
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


