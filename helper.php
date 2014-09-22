<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Tomas Gavenciak <gavento@ucw.cz>
 */

/* must be run within Dokuwiki */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/infoutils.php');


/* composer library autoload */
require_once(DOKU_PLUGIN.'mamweb/vendor/autoload.php');

define('MAMWEB_TPLDIR', __DIR__ . '/tpl/');

class helper_plugin_mamweb extends DokuWiki_Plugin {

    protected $entityManager = null;

    /**
     * Once load and return a Doctrine 2 connected EntityManager
     */
    function getEntityManager() {
	if ($this->entityManager === null) {
	    // Externí soubor s konfigurací doctrine, autoloadingu a DB
	    require_once 'doctrine-config.php';
	    $this->entityManager = getMaMEntityManager();
	}
	return $this->entityManager;
    }

    /**
     * Helper pro volani EntityManager#createQuery a provedeni dotazu
     * Doplni do dotazu navic nekolik spolecnych promennych (ID, ...)
     */
    public function emQuery($query, $params = array()) {
        global $ID;
        $q = $this->getEntityManager()->createQuery($query);
	$ps = array();
	if (strpos($query, ':ID') !== false) { $ps['ID'] = $ID; }
        $q->setParameters($params + $ps);
        return $q->getResult();
    }

    protected $twig = null;

    /**
     * Initialize and return Twig environment
     */

    function getTwigEnvironment() {
	global $ID;
	if ($this->twig == null) {
	    $loader = new Twig_Loader_Filesystem(MAMWEB_TPLDIR);
	    $this->twig = new Twig_Environment($loader, 
		array('strict_variables' => true));

	    /* Dokuwiki a Doctrine obecné funkce */

	    // get_em()
	    $this->twig->addFunction(new Twig_SimpleFunction('get_em',
		function() { return $this->getEntityManager(); }
		));

	    // get_helper()
	    $this->twig->addFunction(new Twig_SimpleFunction('get_helper',
		function() { return $this; }
		));

	    // get_ACT()
	    $this->twig->addFunction(new Twig_SimpleFunction('get_ACT',
		function() { global $ACT; return $ACT; }
		));

	    // get_ID()
	    $this->twig->addFunction(new Twig_SimpleFunction('get_ID',
		function() { global $ID; return $ID; }
		));

	    // wikilink(pageid, text)
	    $this->twig->addFunction(new Twig_SimpleFunction('wikilink',
		function($pageid, $text) { return html_wikilink($pageid, $text); },
		array('is_safe' => array('html'))));

	    // escape(text)
	    $this->twig->addFunction(new Twig_SimpleFunction('escape',
		'htmlspecialchars',
		array('is_safe' => array('html'))));

	    // get_class(x)
	    $this->twig->addFunction(new Twig_SimpleFunction('get_class',
		'get_class'
		));

	    /* Dokuwiki a Doctrine obecné filtry */

	    // |datum
	    $this->twig->addFilter(new Twig_SimpleFilter('datum',
		function($d) { if ($d === null) { return '-'; } return $d->format("d. m. Y"); }
		));

	    // |dump
	    $this->twig->addFilter(new Twig_SimpleFilter('dump',
		function($d) { return substr(var_export($d, true), 0, 200); }
		));

	    /* Funkce specifické pro MaMWeb */

	    // NovyObjekt_wikilink()
	    $this->twig->addFunction(new Twig_SimpleFunction('NovyObjekt_wikilink',
		function() { global $ID; return wl($ID, array('do' => 'mam-novy-objekt')); },
		array('is_safe' => array('html'))));

	    // je_org()
	    $this->twig->addFunction(new Twig_SimpleFunction('je_org',
		function() { return $this->jeOrg(); }
		));


	}
	return $this->twig;
    }

    /**
     * Helper pro vykresleni dane sablony
     * Proda navic nekolik promennych (ID, user_je_org, ...)
     */
    public function twigRender($tpl, $params=array()) {
        return $this->getTwigEnvironment()->render($tpl, $params);
    }

    /**
     * Vrátí true když je daný uživatel (nebo přihlášený) ve skupině 'org'
     */
    function jeOrg($username = null) {
	global $auth;
	global $USERINFO;
	if ($username === null) {
	    $username = $_SERVER['REMOTE_USER'];
	}
	$ud = $auth->getUserData($username);
	return ($ud) && (in_array('org', $ud['grps']));
    }



}
