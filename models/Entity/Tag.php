<?php

namespace Entity;

/**
 * @Entity
 * @Table(name="tagy")
 */
class Tag {
    /** @Id @Column(type="integer") @GeneratedValue **/
    public $id;

    /** @Column(type="string", nullable=false, unique=true) **/
    public $nazev;

    /** @ManyToMany(targetEntity="Entity\Problem", mappedBy="tagy") **/
    public $problemy;

    public function __construct() {
	$this->problemy = new \Doctrine\Common\Collections\ArrayCollection();
    }
}


