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

    /**
     * Problémy s tímto tagem
     *
     * @ManyToMany(targetEntity="Problem", mappedBy="tagy") 
     **/
    private $problemy;
    public function get_problemy() {return $this->problemy; }

    public function __construct($nazev) {
	$this->nazev = $nazev;
	$this->problemy = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


