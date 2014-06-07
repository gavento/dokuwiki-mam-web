<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Tomas Gavenciak <gavento@ucw.cz>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/infoutils.php');


class helper_plugin_mamweb extends DokuWiki_Plugin {

    /**
     * @var PDO initialized via connect()
     */
    protected $dbh = null;

    /**
     * @return PDO connection to the database, or false
     */
    function connect(){
        if ($this->dbh === null) {
	    if ($this->getConf('PDO_data_source') === '') {
                msg('PDO data source not configured', -1);
                return false;
	    }
	    try {
		$dbcon = new PDO($this->getConf('PDO_data_source'));
	    } catch (Exception $e) {
                msg('PDO exception: '.$e->$message, -1);
		return false;
	    }
	    $this->$dbh = $dbcon;
	}
        return $this->$dbh;
    }

}
