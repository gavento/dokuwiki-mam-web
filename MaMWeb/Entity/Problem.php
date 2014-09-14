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
    private $id;

    /**
     * název bez čísla, např 'Poissonova'
     * 
     * @Column(type="string", nullable=false, unique=true)
     **/
    private $nazev;
    public function get_nazev() { return $this->nazev; }
    public function set_nazev($nazev) { $this->nazev = $nazev; }

    /** 
     * Typ: uloha/tema/...
     * 
     * @Column(type="string", nullable=false,
               columnDefinition="VARCHAR(16) CHECK (typ IN ('uloha', 'tema', 'serial', 'org-clanek', 'res-clanek'))")
     **/
    private $typ;
    public function get_typ() { return $this->typ; }
    public function set_typ($typ) { $this->typ = $typ; }

    /**
     * Org-stránka úlohy na wiki
     *
     * @Column(type="string", nullable=false, unique=true)
     **/
    private $pageid;
    public function get_pageid() { return $this->pageid; }
    public function set_pageid($pageid) { $this->pageid = $pageid; }

    /**
     * Stav problému, viz popis tabulky
     *
     * @Column(type="string",
               columnDefinition="VARCHAR(6) CHECK (typ IN ('navrh', 'verejny', 'smazany'))"))
     **/
    private $stav;
    public function get_stav() { return $this->stav; }
    public function set_stav($stav) { $this->stav = $stav; }

    /**
     * link na stránku se zadáním / řešením
     *
     * @Column(type="string")
     **/
    private $verejne_pageid;
    public function get_verejne_pageid() { return $this->verejne_pageid; }
    public function set_verejne_pageid($verejne_pageid) { $this->verejne_pageid = $verejne_pageid; }

    /**
     * Kdo úlohu zadal - username
     *
     * @Column(type="string") 
     **/
    private $zadavatel;
    public function get_zadavatel() { return $this->zadavatel; }
    public function set_zadavatel($zadavatel) { $this->zadavatel = $zadavatel; }

    /**
     * Kdo je přiřazen jako opravovatel - username
     *
     * @Column(type="string")
     **/
    private $opravovatel;
    public function get_opravovatel() { return $this->opravovatel; }
    public function set_opravovatel($opravovatel) { $this->opravovatel = $opravovatel; }

    /**
     * Kód úlohy/tématu v ročníku, např "u2.4" nebo pro "t4", jen pro úlohy a témata
     *
     * @Column(type="string")
     **/
    private $kod_problemu;
    public function get_kod_problemu() { return $this->kod_problemu; }
    public function set_kod_problemu($kod_problemu) { $this->kod_problemu = $kod_problemu; }

    /**
     * maximální počet bodů za úlohu (jen ulohy)
     *
     * @Column(type="integer")
     **/
    private $body;
    public function get_body() { return $this->body; }
    public function set_body($body) { $this->body = $body; }

    /**
     * ve kterém čísle se objevilo zadání úlohy (takto se získá její deadline)
     * nebo první výskyt seriálu, článku, tématu (to ale deadline nemá)
     *
     * @ManyToOne(targetEntity="Cislo", inversedBy="zadane_problemy")
     * @JoinColumn(name="cislo_zadani", referencedColumnName="id")
     **/
    private $cislo_zadani;
    public function get_cislo_zadani() { return $this->cislo_zadani; }
    public function set_cislo_zadani($cislo_zadani) { $this->cislo_zadani = $cislo_zadani; }

    /**
     * ve kterém čísle se mají započítat body za úlohu a objevilo se řešení
     * v případě tématu a jiných NULL
     *
     * @ManyToOne(targetEntity="Cislo", inversedBy="resene_problemy")
     * @JoinColumn(name="cislo_reseni", referencedColumnName="id")
     **/
    private $cislo_reseni;
    public function get_cislo_reseni() { return $this->cislo_reseni; }
    public function set_cislo_reseni($cislo_reseni) { $this->cislo_reseni = $cislo_reseni; }

    /**
     * Datum vzniku navrhu ulohy
     *
     * @Column(type="date")
     **/
    private $datum_vytvoreni;
    public function get_datum_vytvoreni() { return $this->datum_vytvoreni; }

    /**
     * Tagy - pole tagů, hlavně pro M, F, I, L, ale i jiná klíčová slova
     *
     * @ManyToMany(targetEntity="Tag", inversedBy="problemy")
     * @JoinTable(name="problemy_tagy")
     **/
    private $tagy;
    public function get_tagy() { return $this->tagy; }


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


