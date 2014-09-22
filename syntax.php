<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Tomas Gavenciak <gavento@ucw.cz>
 */

if(!defined('DOKU_INC')) die();
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/infoutils.php');

/* composer library autoload */
require_once(__DIR__.'/vendor/autoload.php');


class syntax_plugin_mamweb extends DokuWiki_Syntax_Plugin {

    var $h = null;

    function syntax_plugin_mamweb(){
        $this->h = plugin_load('helper', 'mamweb');
    }

    /** What kind of syntax are we? */
    function getType(){ return 'substition'; }

    /** What about paragraphs? */
    function getPType(){ return 'block'; }

    /** Where to sort in? */
    function getSort(){ return 155; }

    /** Connect pattern to lexer */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{MaM:[^\}]*\}\}', $mode, 'plugin_mamweb');
    }

    /**
     * Zpracuj nalezený tag
     *
     * Syntaxe je obecně {{MaM:Typ:možnosti}}
     *
     * {{MaM:NovyObjekt}} - formulář pro vytvoření nového ročníku, čísla, ...
     * {{MaM:StavDB}} - přehled stavu a potenciálních problému v databázi
     * 
     * @param   string       $match   The text matched by the patterns
     * @param   int          $state   The lexer state for the match
     * @param   int          $pos     The character position of the matched text
     * @param   Doku_Handler $handler The Doku_Handler object
     * @return  array Return an array with all data you want to use in render
     */
    function handle($match, $state, $pos, Doku_Handler & $handler){
	$text = substr($match, 2, -2);
	$parts = explode(':', $text, 3);
	assert($parts[0] == 'MaM');

	return array($parts[1], (count($parts) == 3) ? $parts[2] : '');
    }

    /**
     * Create output or save the data
     * @param   $mode     string        output format being rendered
     * @param   $renderer Doku_Renderer the current renderer object
     * @param   $data     array         data created by handler() -- array(typ, options)
     * @return  boolean                 rendered correctly?
     */
    function render($mode, Doku_Renderer & $renderer, $data) {
        if(is_null($data)) return false;
	if($mode != 'xhtml') return false;

	$typ = $data[0];
	$options = $data[1];

	switch ($typ) {
	    case 'NovyObjekt':
		$renderer->doc .= $this->h->twigRender("form-novy-objekt.html");
		break;
	    case 'StavDB':
		$renderer->doc .= $this->h->twigRender("stav-db.html");
		break;
	    default:
		$renderer->doc .= "<div class='error'>Neznámý MaMWeb tag '" . htmlspecialchars($typ) . "'</div>";
	}
	return true;
    }

}

