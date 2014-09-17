<?php

require_once 'doctrine-config.php';

$em = getMaMEntityManager();

/// !!!!!!!!!!!!!!!!!!!!!!!!!!!! delete database contents
$em->getConnection()->query('DELETE FROM reseni');
$em->getConnection()->query('DELETE FROM resitele');
$em->getConnection()->query('DELETE FROM skoly');
$em->getConnection()->query('DELETE FROM tagy');
$em->getConnection()->query('DELETE FROM problemy');
$em->getConnection()->query('DELETE FROM soustredeni');
$em->getConnection()->query('DELETE FROM cisla');
$em->getConnection()->query('DELETE FROM rocniky');


$r18 = new MaMWeb\Entity\Rocnik(18, "p:r18:index");
$em->persist($r18);
$r19 = new MaMWeb\Entity\Rocnik(19, "p:r19:index");
$em->persist($r19);
$r20 = new MaMWeb\Entity\Rocnik(20, "p:r20:index");
$em->persist($r20);
$r21 = new MaMWeb\Entity\Rocnik(21, "p:r21:index");
$em->persist($r21);

$c201 = new MaMWeb\Entity\Cislo($r20, 1, "p:r20:c1:index");
$c201->set_verejne(true);
$em->persist($c201);
$c202 = new MaMWeb\Entity\Cislo($r20, 2, "p:r20:c2:index");
$c202->set_verejne(true);
$em->persist($c202);
$c211 = new MaMWeb\Entity\Cislo($r21, 1, "p:r21:c1:index");
$c211->set_verejne(true);
$em->persist($c211);

$tM = new MaMWeb\Entity\Tag('M');
$em->persist($tM);
$tF = new MaMWeb\Entity\Tag('F');
$em->persist($tF);
$tI = new MaMWeb\Entity\Tag('I');
$em->persist($tI);
$tKomb = new MaMWeb\Entity\Tag('Kombinatorika');
$em->persist($tKomb);

$p1 = new MaMWeb\Entity\Problem("Navržená úložka", "uloha", "org:p:navrzena-uloha");
$p1->set_zadavatel("seto");
$p1->set_body(4);
$p1->get_tagy()->add($tF);
$em->persist($p1);

$p2 = new MaMWeb\Entity\Problem("Navržené téma", "tema", "org:p:navrzene-tema");
$p2->set_zadavatel("gavento");
$p2->get_tagy()->add($tF);
$em->persist($p2);

$p3 = new MaMWeb\Entity\Problem("Smazany navrh clanku", "org-clanek", "org:p:smazany-clanek");
$p3->set_stav("smazany");
$p3->get_tagy()->add($tI);
$em->persist($p3);

$p4 = new MaMWeb\Entity\Problem("Úloha 1 o matice v c20.1", "uloha", "org:p:zadana-matika");
$p4->set_cislo_zadani($c201);
$p4->set_cislo_reseni($c202);
$p4->set_zadavatel("samo");
$p4->set_opravovatel("seto");
$p4->set_kod($p4->vytvor_kod(1));
$p4->set_verejne_pageid("p:r20:u1.1-o-matice:index");
$p4->set_stav("verejny");
$p4->get_tagy()->add($tM);
$p4->get_tagy()->add($tKomb);
$em->persist($p4);

$p5 = new MaMWeb\Entity\Problem("Téma 21 roč.", "tema", "org:p:tema-roc-21");
$p5->set_cislo_zadani($c211);
$p5->set_zadavatel("gavento");
$p5->set_opravovatel("gavento");
$p5->set_kod($p5->vytvor_kod(1));
$p5->set_verejne_pageid("p:r21:t1-tema-roc-21:index");
$p5->set_stav("verejny");
$em->persist($p5);

$s1 = new MaMWeb\Entity\Skola("Gympl","U gymplu 1", "Chytrakovo", "10000", "CZ");
$em->persist($s1);

$res1 = new MaMWeb\Entity\Resitel("Jan", "Kovář", true, $s1, 2015);
$res1->set_username("kovarik");
$res1->set_telefon("+420 666 666 666");
$em->persist($res1);

$res2 = new MaMWeb\Entity\Resitel("Jana", "Spolužačka", false, $s1, 2016);
$res2->set_email("jo.to@tak.tak");
$res2->set_datum_souhlasu(new \DateTime("now"));
$res2->set_zasilat('domu');
$res2->set_ulice('Doma 42');
$res2->set_mesto('Furthofenburg a/M');
$res2->set_psc('42424');
$res2->set_stat('DE');
$em->persist($res2);

$a1 = new MaMWeb\Entity\Reseni($res2, $p4, $c202, 'papir');
$a1->set_body(3);
$em->persist($a1);

$a2 = new MaMWeb\Entity\Reseni($res1, $p5, $c211, 'email');
$a2->set_pageid('p:r20:u1.1-o-matice:reseni-jan-kovar');
$em->persist($a2);

$sous1 = new MaMWeb\Entity\Soustredeni("sous:vidnava-2014:index", "Vidnava",
    new DateTime("2013-04-22"), new DateTime("2013-04-30"), $r19);
$sous1->set_nazev("Nyní s legendou!");
$em->persist($sous1);


$em->flush();


