<?php

namespace MaMWeb\Entity;

/**
 * @Entity
 * @Table(name="tagy")
 */
class Tag {
    /**
     * internal id
     *
     * @Id @Column(type="integer") @GeneratedValue 
     **/
    private $id;

    /**
     * Název: M/I/F/Kombinatorika/...
     *
     * @Column(type="string", nullable=false, unique=true)
     **/
    private $nazev;
    public function get_nazev() {return $this->nazev; }
    public function set_nazev($nazev) { $this->nazev = $nazev; }

    public function __construct($nazev) {
	$this->nazev = $nazev;
    }
}


