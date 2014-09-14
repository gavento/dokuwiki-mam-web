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
    public function get_id() { return $this->id; }

    /**
     * 1..21..
     * 
     * @Column(type="integer", nullable=false)
     **/
    private $rocnik;
    public function get_rocnik() { return $this->rocnik; }
    public function set_rocnik($rocnik) { $this->rocnik = $rocnik; }

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
     * @Column(type="boolean", nullable=false, options={"defaul": false})
     **/
    private $verejne;
    public function get_verejne() { return $this->verejne; }
    public function set_verejne($verejne) { $this->verejne = $verejne; }

    /**
     * Datum finalni verze, NULL dokud není vydáno
     *
     * @Column(type="date")
     **/
    private $datum_vydani;
    public function get_datum_vydani() { return $this->datum_vydani; }
    public function set_datum_vydani($datum_vydani) { $this->datum_vydani = $datum_vydani; }

    /**
     * Platí pro úlohy zadané v tomto čísle
     * 
     * @Column(type="datetime")
     **/
    private $datum_deadline;
    public function get_datum_deadline() { return $this->datum_deadline; }
    public function set_datum_deadline($datum_deadline) { $this->datum_deadline = $datum_deadline; }

    /**
     * Pole zadanych problemu. Zavisi na Problem.cislo_zadani
     * 
     * @OneToMany(targetEntity="Problem", mappedBy="cislo_zadani")
     **/
    private $zadane_problemy;
    public function get_zadane_problemy() { return $this->zadane_problemy; }

    /**
     * Pole uloh s resenim v tomto cisle. Zavisi na Problem.cislo_reseni
     * 
     * @OneToMany(targetEntity="Problem", mappedBy="cislo_reseni")
     **/
    private $resene_problemy;
    public function get_resene_problemy() { return $this->resene_problemy; }

    /**
     * Povinne parametry jsou rocnik a cislo.
     **/
    public function __construct($rocnik, $cislo) {
	$this->rocnik = $rocnik;
	$this->cislo = $cislo;
	$this->zadane_problemy = new \Doctrine\Common\Collections\ArrayCollection();
	$this->resene_problemy = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


