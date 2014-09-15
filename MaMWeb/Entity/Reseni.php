<?php

namespace MaMWeb\Entity;

/**
 * @Entity @Table(name="reseni")
 */
class Reseni {
    /**
     * Interni MaM id
     * 
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;

    /**
     * Řešitel
     *
     * @ManyToOne(targetEntity="Resitel")
     * @JoinColumn(name="resitel", referencedColumnName="id", nullable=false)
     **/
    private $resitel;
    public function get_resitel() { return $this->resitel; }
    public function set_resitel($resitel) { $this->resitel = $resitel; }

    /**
     * Řešený / bodovaný problém
     *
     * @ManyToOne(targetEntity="Problem")
     * @JoinColumn(name="problem", referencedColumnName="id", nullable=false)
     **/
    private $problem;
    public function get_problem() { return $this->problem; }
    public function set_problem($problem) { $this->problem = $problem; }
 
    /**
     * Pokud je NULL, úloha ještě nebyla opravena
     *
     * @Column(type="integer")
     */
    private $body;
    public function get_body() { return $this->body; }
    public function set_body($body) { $this->body = $body; }

 
    /** ve kterém čísle mají být za řešení uděleny body
     *
     * @ManyToOne(targetEntity="Cislo")
     * @JoinColumn(name="cislo", referencedColumnName="id")
     */
    private $cislo;
    public function get_cislo() { return $this->cislo; }
    public function set_cislo($cislo) { $this->cislo = $cislo; }
 
    /**
     * komentář / log (kdy a kým bylo vyzvednuto ...)
     *
     * @Column(type="string")
     */
    private $komentar;
    public function get_komentar() { return $this->komentar; }
    public function set_komentar($komentar) { $this->komentar = $komentar; }

    /**
     * Forma řešení (papir, email)
     *
     * @Column(type="string", nullable=false,
               columnDefinition="VARCHAR(16) CHECK (forma IN ('papir', 'email'))")
     */
    private $forma;
    public function get_forma() { return $this->forma; }
    public function set_forma($forma) {
	assert(in_array($forma, ["papir", "email"]));
	$this->forma = $forma;
    }
 
    /**
     * (volitelná) stránka s tímto řešením, týká se potenciálně témat a konfer
     *
     * @Column(type="string")
     */
    private $pageid;
    public function get_pageid() { return $this->pageid; }
    public function set_pageid($pageid) { $this->pageid = $pageid; }
    
    /**
     * Datum zaznamenani do DB
     *
     * @Column(type="datetimetz", nullable=false)
     */
    private $datum_vytoreni;
    public function get_datum_vytvoreni() { return $this->datum_vytvoreni; }

    public function __construct($resitel, $problem, $forma) {
	$this->datum_vytvoreni = new DateTime("now");
	$this->set_resitel($resitel);
	$this->set_problem($problem);
	$this->set_forma($forma);
    }
}


