<?php

namespace MaMWeb\Entity;

/**
 * @Entity @Table(name="cisla")
 */
class Cislo {
    /**
     * Interní id
     *
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;

    /**
     * Rocnik cisla
     * 
     * @ManyToOne(targetEntity="Rocnik", inversedBy="cisla")
     * @JoinColumn(name="rocnik", referencedColumnName="id", nullable=false)
     **/
    private $rocnik;
    public function get_rocnik() { return $this->rocnik; }
    public function set_rocnik($rocnik) {
	if ($this->rocnik !== null) {
	    $this->rocnik->get_cisla()->remove($this);
	}
	$this->rocnik = $rocnik;
	$this->rocnik->get_cisla()->add($this);
    }

    /**
     * 1..8, "speciální" vydání (DOD, ...) se nepočítají
     *
     * @Column(type="integer", nullable=false)
     **/
    private $cislo;
    public function get_cislo() { return $this->cislo; }
    public function set_cislo($cislo) { $this->cislo = $cislo; }

    /**
     * Číslo se stává veřejným (pro řešitele) ve chvíli, kdy je připraveno k tisku.
     *
     * @Column(type="boolean", nullable=false)
     **/
    private $verejne;
    public function get_verejne() { return $this->verejne; }
    public function set_verejne($verejne) { $this->verejne = $verejne; }

    /**
     * Stránka čísla na wiki
     *
     * @Column(type="string", nullable=false, unique=true)
     **/
    private $pageid;
    public function get_pageid() { return $this->pageid; }
    public function set_pageid($pageid) { $this->pageid = $pageid; }

    /**
     * Datum finalni verze, NULL dokud není vydáno
     *
     * @Column(type="date", nullable=true)
     **/
    private $datum_vydani;
    public function get_datum_vydani() { return $this->datum_vydani; }
    public function set_datum_vydani($datum_vydani) { $this->datum_vydani = $datum_vydani; }

    /**
     * Platí pro úlohy zadané v tomto čísle
     * 
     * @Column(type="datetimetz", nullable=true)
     **/
    private $datum_deadline;
    public function get_datum_deadline() { return $this->datum_deadline; }
    public function set_datum_deadline($datum_deadline) { $this->datum_deadline = $datum_deadline; }

    /**
     * Pole zadanych problemu. Zavisi na Problem.cislo_zadani
     * TOTO POLE SE NESMI PŘÍMO MODIFIKOVAT (nastavte položky obsažených entit)
     * 
     * @OneToMany(targetEntity="Problem", mappedBy="cislo_zadani")
     **/
    private $zadane_problemy;
    public function get_zadane_problemy() { return $this->zadane_problemy; }

    /**
     * Pole uloh s resenim v tomto cisle. Zavisi na Problem.cislo_reseni
     * TOTO POLE SE NESMI PŘÍMO MODIFIKOVAT (nastavte položky obsažených entit)
     * 
     * @OneToMany(targetEntity="Problem", mappedBy="cislo_reseni")
     **/
    private $resene_problemy;
    public function get_resene_problemy() { return $this->resene_problemy; }

    public function __construct($rocnik, $cislo, $pageid) {
	$this->set_rocnik($rocnik);
	$this->set_cislo($cislo);
	$this->set_pageid($pageid);
	$this->set_verejne(false);
	$this->zadane_problemy = new \Doctrine\Common\Collections\ArrayCollection();
	$this->resene_problemy = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


