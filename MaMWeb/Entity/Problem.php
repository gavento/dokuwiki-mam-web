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
    public function get_id() { return $this->id; }

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
               columnDefinition="VARCHAR(16) NOT NULL CHECK (typ IN ('uloha', 'tema', 'serial', 'org-clanek', 'res-clanek'))")
     **/
    private $typ;
    public function get_typ() { return $this->typ; }
    public function set_typ($typ) {
	assert(in_array($typ, ['uloha', 'tema', 'serial', 'org-clanek', 'res-clanek']));
	$this->typ = $typ;
    }

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
     * @Column(type="string", nullable=false,
               columnDefinition="VARCHAR(16) NOT NULL CHECK (stav IN ('navrh', 'verejny', 'smazany'))"))
     **/
    private $stav;
    public function get_stav() { return $this->stav; }
    public function set_stav($stav) {
	assert(in_array($stav, ['navrh', 'verejny', 'smazany']));
	$this->stav = $stav;
    }

    /**
     * Je objekt (a jeho stránka) viditelný pro ne-orgy?
     */
    public function je_verejny() {
        return ($this->get_stav() === 'verejny');
    }

    /**
     * link na stránku se zadáním / řešením
     *
     * @Column(type="string", nullable=true)
     **/
    private $verejne_pageid;
    public function get_verejne_pageid() { return $this->verejne_pageid; }
    public function set_verejne_pageid($verejne_pageid) { $this->verejne_pageid = $verejne_pageid; }

    /**
     * Kdo úlohu zadal - username
     *
     * @Column(type="string", nullable=true)
     **/
    private $zadavatel;
    public function get_zadavatel() { return $this->zadavatel; }
    public function set_zadavatel($zadavatel) { $this->zadavatel = $zadavatel; }

    /**
     * Kdo je přiřazen jako opravovatel - username
     *
     * @Column(type="string", nullable=true)
     **/
    private $opravovatel;
    public function get_opravovatel() { return $this->opravovatel; }
    public function set_opravovatel($opravovatel) { $this->opravovatel = $opravovatel; }

    /**
     * Kód úlohy/tématu v ročníku, např "u2.4" nebo pro "t4", jen pro úlohy a témata
     *
     * @Column(type="string", nullable=true)
     **/
    private $kod;
    public function get_kod() { return $this->kod; }
    public function set_kod($kod) { $this->kod = $kod; }

    /**
     * maximální počet bodů za úlohu (jen ulohy)
     *
     * @Column(type="integer", nullable=true)
     **/
    private $body;
    public function get_body() { return $this->body; }
    public function set_body($body) { $this->body = $body; }

    /**
     * ve kterém čísle se objevilo zadání úlohy (takto se získá její deadline)
     * nebo první výskyt seriálu, článku, tématu (to ale deadline nemá)
     *
     * @ManyToOne(targetEntity="Cislo", inversedBy="zadane_problemy")
     * @JoinColumn(name="cislo_zadani", referencedColumnName="id", nullable=true)
     **/
    private $cislo_zadani;
    public function get_cislo_zadani() { return $this->cislo_zadani; }
    public function set_cislo_zadani($cislo_zadani) {
        if ($this->cislo_zadani !== null) {
            $this->cislo_zadani->get_zadane_problemy()->remove($this);
        }
        $this->cislo_zadani = $cislo_zadani;
        $this->cislo_zadani->get_zadane_problemy()->add($this);
    }

    /**
     * ve kterém čísle se mají započítat body za úlohu a objevilo se řešení
     * v případě tématu a jiných NULL
     *
     * @ManyToOne(targetEntity="Cislo", inversedBy="resene_problemy")
     * @JoinColumn(name="cislo_reseni", referencedColumnName="id", nullable=true)
     **/
    private $cislo_reseni;
    public function get_cislo_reseni() { return $this->cislo_reseni; }
    public function set_cislo_reseni($cislo_reseni) {
        if ($this->cislo_reseni !== null) {
            $this->cislo_reseni->get_resene_problemy()->remove($this);
        }
        $this->cislo_reseni = $cislo_reseni;
        $this->cislo_reseni->get_resene_problemy()->add($this);
    }

    /**
     * Datum vzniku navrhu ulohy
     *
     * @Column(type="datetimetz", nullable=false)
     **/
    private $datum_vytvoreni;
    public function get_datum_vytvoreni() { return $this->datum_vytvoreni; }

    /**
     * Tagy - pole tagů, hlavně pro M, F, I, L, ale i jiná klíčová slova
     *
     * @ManyToMany(targetEntity="Tag")
     * @JoinTable(name="problemy_tagy")
     **/
    private $tagy;
    public function get_tagy() { return $this->tagy; }

    /**
     * Vrátí kód odpovídající typu, když by měl problém pořadní číslo $cislo
     */
    public function vytvor_kod($cislo) {
	if ($this->typ == 'uloha') {
            return "u{$this->get_cislo_zadani()->get_cislo()}.{$cislo}";
        }
	if ($this->typ == 'tema') {
	    return "t{$cislo}";
	}
	return null;
    }

    public function get_kod_a_nazev() { 
	$k = $this->get_kod();
	return "" . $k . ($k ? ": " : ""). $this->get_nazev();
    }

    public $nazvy_typu = array(
	'uloha' => 'Úloha',
	'tema' => 'Téma',
	'serial' => 'Seriál',
	'org-clanek' => 'Článek',
	'res-clanek' => 'Článek',
    );

    public function get_nazev_typu() {
	return $this->nazvy_typu[$this->get_typ()];
    }

    public function default_pageid($nazev) {
	$n = \MaMWeb\Utils::toURL($nazev);
	return "org:p:{$n}:index";
    }

    public function __construct($nazev, $typ, $pageid) {
	$this->set_nazev($nazev);
	$this->set_typ($typ);
	if ($pageid === null) {
	    $pageid = $this->default_pageid($nazev);
	}
	$this->set_pageid($pageid);
	$this->set_stav('navrh');
	$this->datum_vytvoreni = new \DateTime("now");
	$this->tagy = new \Doctrine\Common\Collections\ArrayCollection();
    }



}


