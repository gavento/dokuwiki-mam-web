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

/**
 * kod_problemu TODO
 */
function kod_problemu($res) {
    if ($res['cislo_problemu'] === null) return '';
    if ($res['typ'] == 'uloha') {
	return 'u' . $res['cislo_problemu'];
    }
    return 't' . $res['cislo_problemu'];
}

class action_plugin_mamweb extends DokuWiki_Action_Plugin {
 
    var $helper = null;
    var $dbh = null;
    var $twig = null;

    public function __construct() {
        $this->helper = plugin_load('helper', 'mamweb');
	$this->dbh = $this->helper->connect();
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
     * Register the eventhandlers
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'mam_headers', array ());
    }
 
    /**
     * Show/generate MaM headers if any are appropriate
     */
    public function mam_headers(& $event, $param) {
	$this->problem_org_header($event->data);
	$this->problem_public_header($event->data);
	$this->cislo_header($event->data);
    }

    /**
     * Hlavicky orgovske stranky problemu/ulohy, je-li odpovidajici v databazi
     */
    private function problem_org_header($action) {
	if (($action == 'show') || ($action == 'edit')) {
	    $r = $this->emQuery('SELECT p FROM Entity\Problem p WHERE p.pageid = :ID');
	    if ($r) {
		ptln($this->twigRender('problem-header.html',
		    array('org_page' => true, 'edit' => ($action == 'edit'), 'p' => $r[0] )));
	    }
	}
    }

    /**
     * Hlavicky verejne stranky problemu/ulohy, je-li odpovidajici v databazi
     */
    private function problem_public_header($action) {
	if (($action == 'show') || ($action == 'edit')) {
	    $r = $this->emQuery('SELECT p FROM Entity\Problem p WHERE p.verejne_pageid = :ID');
	    if ($r) {
		ptln($this->twigRender('problem-header.html',
		    array('org_page' => false, 'edit' => false, 'p' => $r[0] )));
	    }
	}
    }

    /**
     * Hlavicky stranky cisla, je-li odpovidajici v databazi
     */
    private function cislo_header($action) {
    }




    /////// Obsolete

    public function query_one($query, $params = array()) {
	global $ID;
	$q = $this->dbh->prepare($query);
	if (! $q) {
	    msg('Query preparation failed: '.$query);
	} else {
	    $r = $q->execute($params + array('ID' => $ID));
	    if (! $r) {
	        msg('Query execution failed: '.$query.', params: '.$params);
	    } else {
	    	return $q->fetch(PDO::FETCH_ASSOC);
	    }
	}
    }
}
