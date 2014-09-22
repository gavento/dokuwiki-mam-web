<?php

namespace MaMWeb\Entity;

/**
 * @Entity @Table(name="soustredeni")
 */
class Soustredeni {
    /**
     * Interni MaM id
     * 
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;

    /**
     * Ročník, automaticky odvozený z datumů
     *
     * @ManyToOne(targetEntity="Rocnik", inversedBy="soustredeni")
     * @JoinColumn(name="rocnik", referencedColumnName="id", nullable=false)
     **/
    private $rocnik;
    public function get_rocnik() { return $this->rocnik; }
    public function set_rocnik($rocnik) {
        if ($this->rocnik !== null) {
            $this->rocnik->get_soustredeni()->remove($this);
        }
        $this->rocnik = $rocnik;
        $this->rocnik->get_soustredeni()->add($this);
    }

    /**
     * Stránka soustředka a galerie na Wiki
     *
     * @Column(type="string", unique=true, nullable=true)
     */
    private $pageid;
    public function get_pageid() { return $this->pageid; }
    public function set_pageid($pageid) { $this->pageid = $pageid; }

    /**
     * Název místa / obce
     *
     * @Column(type="string", nullable=false)
     */
    private $misto;
    public function get_misto() { return $this->misto; }
    public function set_misto($misto) { $this->misto = $misto; }

    /**
     * Název tématu soustředění
     *
     * @Column(type="string", nullable=true)
     */
    private $nazev;
    public function get_nazev() { return $this->nazev; }
    public function set_nazev($nazev) { $this->nazev = $nazev; }
    
    /**
     * Datum zacatku s presnosti na dny
     *
     * @Column(type="date", nullable=true)
     */
    private $datum_zacatku;
    public function get_datum_zacatku() { return $this->datum_zacatku; }
    public function set_datum_zacatku($datum_zacatku) { $this->datum_zacatku = $datum_zacatku; }

    /**
     * Datum konce s presnosti na dny
     *
     * @Column(type="date", nullable=true)
     */
    private $datum_konce;
    public function get_datum_konce() { return $this->datum_konce; }
    public function set_datum_konce($datum_konce) { $this->datum_konce = $datum_konce; }

    /**
     * Je objekt (a jeho stránka) viditelný pro ne-orgy?
     */
    public function je_verejny() {
        return true;
    }

    public function default_pageid($rocnik, $misto) {
	$n = \MaMWeb\Utils::toURL($misto);
	return "sous:r{$rocnik->get_rocnik()}-{$n}:index";
    }

    public function __construct($rocnik, $misto, $pageid) {
	$this->set_rocnik($rocnik);
	$this->set_misto($misto);
	if ($pageid === null) {
	    $pageid = $this->default_pageid($rocnik, $misto);
	}
	$this->set_pageid($pageid);
    }
}


