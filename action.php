<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Tomas Gavenciak <gavento@ucw.cz>
 */

if (!defined('DOKU_INC')) die();
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/infoutils.php');

/* composer library autoload */
require_once(__DIR__.'/vendor/autoload.php');


class action_plugin_mamweb extends DokuWiki_Action_Plugin {
 
    var $h = null;
    var $twig = null;
    var $em = null;

    public function __construct() {
        $this->h = plugin_load('helper', 'mamweb');
	$this->twig = $this->h->getTwigEnvironment();
	$this->em = $this->h->getEntityManager();
    }

    /**
     * Pokud $query vrátí výsledek, pak vypíše šablonu $tpl s výsledkem jakožto $param_name a
     * dalšími parametry. Při nenalezení nedělá nic.
     */
    private function hlavicka_test($query, $param_name, $tpl, $tpl_params = array(), & $event=null) {
        global $ID;
	$r = $this->h->emQuery($query);
	if ($r) {
	    $obj = $r[0];
	    ptln($this->h->twigRender($tpl, array($param_name => $obj) + $tpl_params));
	    // Pokud nemá stránka obsah, zobraz jen varování (a jen orgovi) či nic
	    if ((! page_exists($ID)) && ($event !== null) && ($event->data == 'show')) {
	        ptln($this->h->twigRender("prazdna-stranka-objektu.html"));
	        $event->preventDefault();
	    }
	}
    }

    /**
     * Generuj halvičky MaM entit pro aktuální stránku
     */
    public function mam_hlavicky(& $event, $param) {
        $action = $event->data;

	// Obsluhujeme jen 'edit' a 'show'
        if (! in_array($action, ['show', 'edit'])) {
	    return;
	}

	// Otestovat a zobrazit případné hlavičky

	// Org stránka problému
	$this->hlavicka_test('SELECT p FROM MaMWeb\Entity\Problem p WHERE p.pageid = :ID',
	                     'p', 'hlavicka-problem-org.html', ['edit' => $edit], $event);

	// Veřejná stránka problému
	$this->hlavicka_test('SELECT p FROM MaMWeb\Entity\Problem p WHERE p.verejne_pageid = :ID',
	                     'p', 'hlavicka-problem-verejna.html', ['edit' => $edit], $event);

	// Stránka čísla
	$this->hlavicka_test('SELECT c FROM MaMWeb\Entity\Cislo c WHERE c.pageid = :ID',
	                     'c', 'hlavicka-cislo.html', ['edit' => $edit], $event);

	// Stránka ročníku
	$this->hlavicka_test('SELECT r FROM MaMWeb\Entity\Rocnik r WHERE r.pageid = :ID',
	                     'r', 'hlavicka-rocnik.html', ['edit' => $edit], $event);

	// Stránka soustředění
	$this->hlavicka_test('SELECT s FROM MaMWeb\Entity\Soustredeni s WHERE s.pageid = :ID',
	                     's', 'hlavicka-soustredeni.html', ['edit' => $edit], $event);

	// Stránka řešení
	$this->hlavicka_test('SELECT res FROM MaMWeb\Entity\Reseni res WHERE res.pageid = :ID',
	                     'res', 'hlavicka-reseni.html', ['edit' => $edit], $event);
    }


    /**
     * Pokud $query vrátí výsledek, pak se otestuje ->je_verejny() a pripadne
     * nastavi nulova prava.
     */
    private function verejne_test($query, & $event) {
	$r = $this->h->emQuery($query);
	if ($r) {
	    $obj = $r[0];
	    if (! $obj->je_verejny()) {
		$event->result = AUTH_NONE;
	    }
	}
    }

    /**
     * Generuj halvičky MaM entit pro aktuální stránku
     */
    public function mam_verejne(& $event, $param) {
        $action = $event->data;

	if ($this->h->jeOrg()) {
	    return;
	}

	// Uživatel není org - testy na veřejnost stránek

	// Org stránka problému
	$this->verejne_test('SELECT p FROM MaMWeb\Entity\Problem p WHERE p.pageid = :ID', $event);

	// Veřejná stránka problému
	$this->verejne_test('SELECT p FROM MaMWeb\Entity\Problem p WHERE p.verejne_pageid = :ID', $event);

	// Stránka čísla
	$this->verejne_test('SELECT c FROM MaMWeb\Entity\Cislo c WHERE c.pageid = :ID', $event);

	// Stránka ročníku
	$this->verejne_test('SELECT r FROM MaMWeb\Entity\Rocnik r WHERE r.pageid = :ID', $event);

	// Stránka soustředění
	$this->verejne_test('SELECT s FROM MaMWeb\Entity\Soustredeni s WHERE s.pageid = :ID', $event);

	// Stránka řešení
	$this->verejne_test('SELECT res FROM MaMWeb\Entity\Reseni res WHERE res.pageid = :ID', $event);
    }

    /**
     * Ošetři vytvoření nového objektu
     */
    public function mam_novy_objekt(& $event, $param) {
	global $ID;
	global $INFO;

	if ($event->data != 'mam-novy-objekt') return;

	if (!$this->h->jeOrg()) {
	    $event->data = 'show';
	}
	$event->stopPropagation();
	$event->preventDefault();

	$typ_entity = $_REQUEST['typ_entity'];

	$entita = null;
	switch ($typ_entity) {
	    case 'rocnik':
		$rocnik = (int)($_REQUEST['rocnik']);
		$entita = new \MaMWeb\Entity\Rocnik($rocnik, null);
		break;
	    case 'cislo':
		$rocnik_id = (int)($_REQUEST['rocnik_id']);
		$rocnik = $this->em->find('\MaMWeb\Entity\Rocnik', $rocnik_id);
		$cislo = (int)($_REQUEST['cislo']);
		$entita = new \MaMWeb\Entity\Cislo($rocnik, $cislo, null);
		break;
	    case 'soustredeni':
		$rocnik_id = (int)($_REQUEST['rocnik_id']);
		$rocnik = $this->em->find('\MaMWeb\Entity\Rocnik', $rocnik_id);
		$misto = $_REQUEST['misto'];
		$entita = new \MaMWeb\Entity\Soustredeni($rocnik, $misto, null);
		break;
	    default:
		msg('Špatné parametry akce "mam-novy-objekt".', -1);
		$event->data = 'show';
		return;
	}

	assert ($entita !== null);
	try {
	    $this->em->persist($entita);
	    $this->em->flush();
	} catch (\Doctrine\DBAL\DBALException $e) {
	    msg('Chyba: ' . $e->GetMessage(), -1);
	    $event->data = 'show';
	    return;
	}
	$pageid = $entita->get_pageid();
	header("HTTP/1.1 303 See Other");
	header("Location: " . wl($pageid, array('do' => 'edit')));
	return;
    }

     /**
     * Register the eventhandlers
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'mam_hlavicky', array ());
        $controller->register_hook('AUTH_ACL_CHECK', 'AFTER', $this, 'mam_verejne', array ());
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'mam_novy_objekt', array ());
    }

}
