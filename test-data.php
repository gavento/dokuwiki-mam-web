<?php

require_once 'doctrine-config.php';

$em = getMaMEntityManager();

$r19 = new MaMWeb\Entity\Rocnik(2012);
$em->persist($r19);
$r20 = new MaMWeb\Entity\Rocnik(2013);
$em->persist($r20);
$r21 = new MaMWeb\Entity\Rocnik(2014);
$em->persist($r21);

$c201 = new MaMWeb\Entity\Cislo($r20, 1);
$em->persist($c201);
$c202 = new MaMWeb\Entity\Cislo($r20, 2);
$em->persist($c202);
$c211 = new MaMWeb\Entity\Cislo($r21, 1);
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
$p4->set_kod_problemu($p4->sablona_kod_problemu(1));
$p4->set_verejne_pageid("p:zadana-matika");
$p4->set_stav("verejny");
$p4->get_tagy()->add($tM);
$p4->get_tagy()->add($tKomb);
$em->persist($p4);

$p5 = new MaMWeb\Entity\Problem("Téma 21 roč.", "tema", "org:p:tema-roc-21");
$p5->set_cislo_zadani($c211);
$p5->set_zadavatel("gavento");
$p5->set_opravovatel("gavento");
$p5->set_kod_problemu($p5->sablona_kod_problemu(1));
$p5->set_verejne_pageid("p:zadana-matika");
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
$a2->set_pageid('r:zadana-matika-1');
$em->persist($a2);

$em->flush();


