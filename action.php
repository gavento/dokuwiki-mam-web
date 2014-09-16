<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Tomas Gavenciak <gavento@ucw.cz>
 */

if (!defined('DOKU_INC')) die();
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/infoutils.php');

/* composer library autoload */
require_once(__DIR__.'/vendor/autoload.php');

/**
 * Return true if named (or current by default) user is in group 'org'
 */
function user_is_org($username = null) {
    global $auth;
    global $USERINFO;
    if ($username === null) {
	$username = $_SERVER['REMOTE_USER'];
    }
    $ud = $auth->getUserData($username);
    return ($ud) && (in_array('org', $ud['grps']));
}

class action_plugin_mamweb extends DokuWiki_Action_Plugin {
 
    var $helper = null;
    var $twig = null;

    public function __construct() {
        $this->helper = plugin_load('helper', 'mamweb');
	$this->twig = $this->helper->getTwigEnvironment();
	$this->em = $this->helper->getEntityManager();
    }

    /**
     * Helper pro volani EntityManager#createQuery a provedeni dotazu
     * Doplni do dotazu navic nekolik spolecnych promennych (ID, ...)
     */
    public function emQuery($query, $params = array()) {
	global $ID;
	$q = $this->em->createQuery($query);
	$q->setParameters(array('ID' => $ID) + $params);
	return $q->getResult();
    }

    /**
     * Helper pto vykresleni dane sablony
     * Proda navic nekolik promennych (ID, user_org, ...)
     */
    private function twigRender($tpl, $params=array()) {
	$common = array(
	    'user_org' => user_is_org(),
	    'ID' => $ID,
	);
	return $this->twig->render($tpl, $common + $params);
    }

    /**
     * Pokud $query vrátí výsledek, pak vypíše šablonu $tpl s výsledkem jakožto $param_name a
     * dalšími parametry. Při nenalezení nedělá nic.
     */
    private function hlavicka_test($query, $param_name, $tpl, $tpl_params = array()) {
	$r = $this->emQuery($query);
	if ($r) {
	    ptln($this->twigRender($tpl, array($param_name => $r[0]) + $tpl_params));
	}
    }

    /**
     * Register the eventhandlers
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'mam_headers', array ());
    }
 
    /**
     * Show/generate MaM headers if any are appropriate
     */
    public function mam_headers(& $event, $param) {
        $action = $event->data;

	// Obsluhujeme jen 'edit' a 'show'
        if (! in_array($action, ['show', 'edit'])) {
	    return;
	}

	// Pokud nemá user práva k editaci, tak se mu ukáže jen R/O info
	if (($action === 'edit') && (auth_quickaclcheck($ID) < AUTH_EDIT)) {
	    $action = 'show';
	}
	$edit = ($action === 'edit');

	// Otestovat a zobrazit případné hlavičky

	// Org stránka problému
	$this->hlavicka_test('SELECT p FROM MaMWeb\Entity\Problem p WHERE p.pageid = :ID',
	                     'p', 'hlavicka-problem-org.html', ['edit' => $edit]);

	// Veřejná stránka problému
	$this->hlavicka_test('SELECT p FROM MaMWeb\Entity\Problem p WHERE p.verejne_pageid = :ID',
	                     'p', 'hlavicka-problem-verejna.html', ['edit' => $edit]);

	// Stránka čísla
	$this->hlavicka_test('SELECT c FROM MaMWeb\Entity\Cislo c WHERE c.pageid = :ID',
	                     'c', 'hlavicka-cislo.html', ['edit' => $edit]);

	// Stránka ročníku
	$this->hlavicka_test('SELECT r FROM MaMWeb\Entity\Rocnik r WHERE r.pageid = :ID',
	                     'r', 'hlavicka-rocnik.html', ['edit' => $edit]);

	// Stránka soustředění
	$this->hlavicka_test('SELECT s FROM MaMWeb\Entity\Soustredeni s WHERE s.pageid = :ID',
	                     's', 'hlavicka-soustredeni.html', ['edit' => $edit]);

	// Stránka řešení
	$this->hlavicka_test('SELECT res FROM MaMWeb\Entity\Reseni res WHERE res.pageid = :ID',
	                     'res', 'hlavicka-reseni.html', ['edit' => $edit]);
    }

}
