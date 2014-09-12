<?php

namespace Entity;

/**
 * @Entity @Table(name="cisla")
 */
class Cislo {
    /** @Id @Column(type="integer", nullable=false, unique=true) @GeneratedValue **/
    public $id;

    /** @Column(type="integer", nullable=false) **/
    public $rocnik;

    /** @Column(type="integer", nullable=false) **/
    public $cislo;

    /** @Column(type="boolean") **/
    public $verejne;

    /** @Column(type="date") **/
    public $datum_vydani;

    /** @Column(type="datetime") **/
    public $datum_deadline;

    /** @OneToMany(targetEntity="Entity\Problem", mappedBy="prvni_cislo") **/
    public $zadane_problemy;

    /** @OneToMany(targetEntity="Entity\Problem", mappedBy="reseni_cislo") **/
    public $resene_problemy;


    public function __construct() {
	//$this->datum_vytvoreni=
	$this->zadane_problemy = new \Doctrine\Common\Collections\ArrayCollection();
	$this->resene_problemy = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


