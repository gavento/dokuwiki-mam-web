<?php
/**
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Tomas Gavenciak <gavento@ucw.cz>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_mamweb extends DokuWiki_Syntax_Plugin {

    var $mamhelper = null;

    /**
     * Constructor. Load helper plugin
     */
    function syntax_plugin_mamweb(){
        $this->mamhelper = plugin_load('helper', 'mamweb');
    }

    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }

    /**
     * What about paragraphs?
     */
    function getPType(){
        return 'block';
    }

    /**
     * Where to sort in?
     */
    function getSort(){
        return 155;
    }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('----+ *mamweb[^\n]*?----+\n.*?\n----+', $mode, 'plugin_mamweb');
    }

    /**
     * Handle the match - parse the data
     */
    function handle($match, $state, $pos, Doku_Handler &$handler){

        // get lines
        $lines = explode("\n", $match);
        array_pop($lines);
        $class = array_shift($lines);
        $class = str_replace('mamweb', '', $class);
        $class = trim($class, '- ');

        // parse info
        $data = array();
        $columns = array();
        foreach ( $lines as $line ) {
            $line = trim($line);
            if(empty($line)) continue;
            $line = preg_split('/\s*:\s*/', $line, 2);

            if (sizeof($line) < 2) {
	        msg("Line without ':'. ", -1);
		return null;
	    }
            $data[$line[0]] = $line[1];
        }
	msg("Class: $class");
        return array('data'=>$data, 'class'=>$class,
                     'pos' => $pos, 'len' => strlen($match)); // not utf8_strlen
    }

    /**
     * Create output or save the data
     */
    function render($format, Doku_Renderer &$renderer, $data) {
        if(is_null($data)) return false;

        global $ID;
        switch ($format){
            case 'xhtml':
                /** @var $renderer Doku_Renderer_xhtml */
                $this->_showData($data, $ID, $renderer);
                return true;
            case 'metadata':
                /** @var $renderer Doku_Renderer_metadata */
#                $this->_saveData($data, $ID, $renderer);
                return true;
            default:
                return false;
        }
    }

    function _showData($data, $pageid, $R) {
        $datastr=var_export($data, true);
	$R->doc .= "<p>MaMPlugin at $pageid, class: ${data['class']}, data: $datastr</p>";
    }

}

