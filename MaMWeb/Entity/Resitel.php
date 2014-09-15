<?php

namespace MaMWeb\Entity;

/**
 * @Entity @Table(name="resitele")
 */
class Resitel {
    /**
     * Interni MaM id
     * 
     * @Id @Column(type="integer") @GeneratedValue
     **/
    private $id;

    /**
     * @Column(type="string", nullable=false)
     **/
    private $jmeno;
    public function get_jmeno() { return $this->jmeno; }
    public function set_jmeno($jmeno) { $this->jmeno = $jmeno; }

    /**
     * @Column(type="string", nullable=false)
     **/
    private $prijmeni;
    public function get_prijmeni() { return $this->prijmeni; }
    public function set_prijmeni($prijmeni) { $this->prijmeni = $prijmeni; }

    /**
     * Username, pokud ma wiki-ucet.
     *
     * @Column(type="string", unique=true)
     **/
    private $username;
    public function get_username() { return $this->username; }
    public function set_username($username) { $this->username = $username; }

    /**
     * Pohlaví, že ho neznáme se snad nestane (a ušetří to práci při programování) 
     *
     * @Column(type="boolean", nullable=false)
     **/
    private $pohlavi_muz;
    public function get_pohlavi_muz() { return $this->pohlavi_muz; }
    public function set_pohlavi_muz($pohlavi_muz) { $this->pohlavi_muz = $pohlavi_muz; }

    /**
     * Škola
     *
     * @ManyToOne(targetEntity="Skola")
     * @JoinColumn(name="skola", referencedColumnName="id", nullable=false)
     **/
    private $skola;
    public function get_skola() { return $this->skola; }
    public function set_skola($skola) { $this->skola = $skola; }
    

    /** 
     * Očekávaný rok maturity
     *
     * @Column(type="integer", nullable=false)
     **/
    private $rok_maturity;
    public function get_rok_maturity() { return $this->rok_maturity; }
    public function set_rok_maturity($rok_maturity) { $this->rok_maturity = $rok_maturity; }

    /** Kontakty a detaily, pokud známe **/

    /**
     * @Column(type="string")
     **/
    private $email;
    public function get_email() { return $this->email; }
    public function set_email($email) { $this->email = $email; }

    /**
     * @Column(type="string")
     **/
    private $telefon;
    public function get_telefon() { return $this->telefon; }
    public function set_telefon($telefon) { $this->telefon = $telefon; }

    /**
     * @Column(type="date")
     **/
    private $datum_narozeni;
    public function get_datum_narozeni() { return $this->datum_narozeni; }
    public function set_datum_narozeni($datum_narozeni) { $this->datum_narozeni = $datum_narozeni; }

    /** Souhlasy - NULL dokud nedali souhlas **/

    /**
     * Souhlas se zpracováním osobních údajů
     *
     * @Column(type="date")
     **/
    private $datum_souhlasu;
    public function get_datum_souhlasu() { return $this->datum_souhlasu; }
    public function set_datum_souhlasu($datum_souhlasu) { $this->datum_souhlasu = $datum_souhlasu; }

    /**
     * Souhlas se zasíláním fakultních informací.
     *
     * @Column(type="date")
     **/
    private $datum_souhlasu_spam;
    public function get_datum_souhlasu_spam() { return $this->datum_souhlasu_spam; }
    public function set_datum_souhlasu_spam($datum_souhlasu_spam) { $this->datum_souhlasu_spam = $datum_souhlasu_spam; }

    /**
     * Datul připojení řešitele k MaM
     *
     * @Column(type="date", nullable=false)
     **/
    private $datum_prihlaseni;
    public function get_datum_prihlaseni() { return $this->datum_prihlaseni; }
    public function set_datum_prihlaseni($datum_prihlaseni) { $this->datum_prihlaseni = $datum_prihlaseni; }

    /**
     * Kam zasílat papírové řešení
     *
     * @Column(type="string", nullable=false,
               columnDefinition="VARCHAR(16) CHECK (zasilat IN ('domu', 'doskoly', 'nikam'))")
     **/
    private $zasilat;
    public function get_zasilat() { return $this->zasilat; }
    public function set_zasilat($zasilat) {
	assert(in_array($zasilat, ['domu', 'doskoly', 'nikam']));
	$this->zasilat = $zasilat;
    }

    /** Adresa, pokud ji známe. Ulice může být jen číslo **/

    /** @Column(type="string", nullable=true) **/
    private $ulice;
    public function get_ulice() { return $this->ulice; }
    public function set_ulice($ulice) { $this->ulice = $ulice; }
    /** @Column(type="string", nullable=true) **/
    private $mesto;
    public function get_mesto() { return $this->mesto; }
    public function set_mesto($mesto) { $this->mesto = $mesto; }
    /** @Column(type="string", nullable=true) **/
    private $psc;
    public function get_psc() { return $this->psc; }
    public function set_psc($psc) { $this->psc = $psc; }

    /**
     * ISO 3166-1 dvojznakovy kod zeme velkym pismem (CZ, SK)
     * 
     * @Column(type="string", nullable=true)
     **/
    private $stat;
    public function get_stat() { return $this->stat; }
    public function set_stat($stat) {
	assert((strlen($stat) == 2));
	$this->stat = strtoupper($stat);
    }


    public function __construct($jmeno, $prijmeni, $pohlavi_muz, $skola, $rok_maturity) {
	$this->datum_prihlaseni = new DateTime("now");
	$this->set_jmeno($jmeno);
	$this->set_prijmeni($prijmeni);
	$this->set_pohlavi_muz($pohlavi_muz);
	$this->set_skola($skola);
	$this->set_rok_maturity($rok_maturity);
    }
}


