<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Tomas Gavenciak <gavento@ucw.cz>
 */

if (!defined('DOKU_INC')) die();
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/infoutils.php');

 
class action_plugin_mamweb extends DokuWiki_Action_Plugin {
 
    var $helper = null;
    var $dbh = null;

    public function __construct() {
        $this->helper = plugin_load('helper', 'mamweb');
        $this->dbh = $this->helper->connect();
    }

    /**
     * Register the eventhandlers
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'mam_headers', array ());
    }
 
    /**
     * Show MaM headers if any are appropriate
     */
    public function mam_headers(& $event, $param) {
	if ($event->data == 'show' || $event->data == 'edit') {
	    $this->uloha_org_header($event->data);
	    $this->uloha_public_header($event->data);
	    $this->cislo_header($event->data);
        }
    }
    
    public function query_one($query, $params) {
	$q = $this->dbh->prepare($query);
	if (! $q) {
	    msg('Query preparation failed: '.$query);
	} else {
	    $r = $q->execute($params);
	    if (! $r) {
	        msg('Query execution failed: '.$query.', params: '.$params);
	    } else {
	    	return $q->fetch(PDO::FETCH_ASSOC);
	    }
	}
    }

    /**
     * Hlavicky orgovske stranky ulohy, je-li odpovidajici v databazi
     */
    private function uloha_org_header($action) {
//	var $r;
	global $ID;
	$r = $this->query_one('SELECT * FROM problemy WHERE pageid = :ID', array(':ID' => $ID));
	if ($r) {
	    if ($action == 'show') {
	        ptln('<h2>Problém jménem "'.$r['nazev'].'" typu '.$r['typ'].'</h2>');
		ptln('<table>');
	        ptln('<tr><th>Vytvořený<td>'.$r['datum_vytvoreni'].' uživatelem <code>'.$r['zadavatel'].'</code></tr>');
	        ptln('<tr><th>Bodů<td>'.$r['body'].'</tr>');
	        ptln('<tr><th>Stav<td>'.$r['stav'].'</tr>');
	        ptln('</table>');
	    }
	    if ($action == 'edit') {
	        ptln('<h2>Problém <input type="text" value="'.$r['nazev'].'"></input> typu '.$r['typ'].'</h2>');
		ptln('<table>');
	        ptln('  <tr><th>Vytvořený<td>'.$r['datum_vytvoreni'].' uživatelem <code>'.$r['zadavatel'].'</code></tr>');
	        ptln('  <tr><th>Bodů<td><input type="text" value="'.$r['body'].'"></input></tr>');
	        ptln('  <tr><th>Stav<td><select>');
		ptln('    <option value="navrh" '.  ($r['stav'] == 'navrh'?  'selected':'').'>navrh</option>');
		ptln('    <option value="verejny" '.($r['stav'] == 'verejny'?'selected':'').'>verejny</option>');
		ptln('    <option value="smazany" '.($r['stav'] == 'smazany'?'selected':'').'>smazany</option>');
		ptln('  </select></tr>');
	        ptln('</table>');
	    }

	}
    }


    /**
     * Hlavicky verejne stranky ulohy, je-li odpovidajici v databazi
     */
    private function uloha_public_header($action) {
    }

    /**
     * Hlavicky stranky cisla, je-li odpovidajici v databazi
     */
    private function cislo_header($action) {
    }
 
}
