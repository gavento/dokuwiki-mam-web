<?php

namespace Entity;

/**
 * @Entity @Table(name="problemy")
 */
class Problem {
    /** @Id @Column(type="integer") @GeneratedValue **/
    public $id;

    /** @Column(type="string") **/
    public $nazev;

    /** @Column(type="string") **/
    public $pageid;

    /** @Column(type="string") **/
    public $verejne_pageid;

    /** @Column(type="string") **/
    public $typ;

    /** @Column(type="string") **/
    public $zadavatel;

    /** @Column(type="string") **/
    public $opravovatel;

    /** @Column(type="string") **/
    public $stav;

    /** @Column(type="integer") **/
    public $cislo_problemu;

    /** @Column(type="integer") **/
    public $body;

    /** @Column(type="date") **/
    public $datum_vytvoreni;

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


