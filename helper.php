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

    protected $twig = null;

    /**
     * Initialize and return Twig environment
     */

    function getTwigEnvironment() {
	if ($this->twig == null) {
	    $loader = new Twig_Loader_Filesystem(MAMWEB_TPLDIR);
	    $this->twig = new Twig_Environment($loader, 
		array('strict_variables' => true));
	    $wikilink_f = new Twig_SimpleFunction('wikilink', function($pageid, $text) {
		    return html_wikilink($pageid, $text);
		}, array('is_safe' => array('html')));
	    $this->twig->addFunction($wikilink_f);
	    $escape_f = new Twig_SimpleFunction('escape', 'htmlspecialchars', array('is_safe' => array('html')));
	    $this->twig->addFunction($escape_f);
	}
	return $this->twig;
    }

}
