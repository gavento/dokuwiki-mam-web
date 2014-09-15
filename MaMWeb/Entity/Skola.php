<?php

namespace MaMWeb\Entity;

/**
 * Mělo by být částečně vytaženo z Aesopa, viz https://ovvp.mff.cuni.cz/wiki/aesop/export-skol.
 *
 * @Entity @Table(name="skoly")
 **/
class Skola {
    /**
     * interní id v MaM
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;

    /**
     * aesopi ID
     * @Column(type="string", unique=true, nullable=false)
     **/
    private $aesop_id;
    public function get_aesop_id() { return $this->aesop_id; }
    public function set_aesop_id($aesop_id) { $this->aesop_id = $aesop_id; }

    /**
     * IZO školy (jen české školy)
     *
     * @Column(type="string", nullable=true)
     **/
    private $izo;
    public function get_izo() { return $this->izo; }
    public function set_izo($izo) { $this->izo = $izo; }

    /**
     * Celý název školy
     *
     * @Column(type="string", nullable=false)
     **/
    private $nazev;
    public function get_nazev() { return $this->nazev; }
    public function set_nazev($nazev) { $this->nazev = $nazev; }

    /**
     * Zkraceny nazev pro zobrazení ve výsledkovce, volitelné.
     * Není v Aesopovi, musíme vytvářet sami.
     *
     * @Column(type="string", nullable=true)
     **/
    private $kratky_nazev;
    public function get_kratky_nazev() { return $this->kratky_nazev; }
    public function set_kratky_nazev($kratky_nazev) { $this->kratky_nazev = $kratky_nazev; }

    /** Adresa. Ulice může být jen číslo **/

    /** @Column(type="string", nullable=false) **/
    private $ulice;
    public function get_ulice() { return $this->ulice; }
    public function set_ulice($ulice) { $this->ulice = $ulice; }
    /** @Column(type="string", nullable=false) **/
    private $mesto;
    public function get_mesto() { return $this->mesto; }
    public function set_mesto($mesto) { $this->mesto = $mesto; }
    /** @Column(type="string", nullable=false) **/
    private $psc;
    public function get_psc() { return $this->psc; }
    public function set_psc($psc) { $this->psc = $psc; }

    /**
     * ISO 3166-1 dvojznakovy kod zeme velkym pismem (CZ, SK)
     * 
     * @Column(type="string", nullable=false)
     **/
    private $stat;
    public function get_stat() { return $this->stat; }
    public function set_stat($stat) {
        assert((strlen($stat) == 2));
        $this->stat = strtoupper($stat);
    }

    public function __construct($nazev, $ulice, $mesto, $psc, $stat) {
	$this->set_nazev($nazev);
	$this->set_ulice($ulice);
	$this->set_mesto($mesto);
	$this->set_psc($psc);
	$this->set_stat($stat);
	$this->set_aesop_id("ufo");
    }
}


