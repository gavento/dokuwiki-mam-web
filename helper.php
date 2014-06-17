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

define('MAMWEB_UPDATEDIR',DOKU_PLUGIN.'mamweb/db/');


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
                msg('PDO exception: '.$e->getMessage(), -1);
		return false;
	    }
	    $this->dbh = $dbcon;
	}

	// Check current MaM DB version (if any)
	$ver = $this->_getDbVersion();
	msg("MaM-DB version found: $ver", 0);

	// Update if newer version available
	for ($v = $ver+1; $file = sprintf(MAMWEB_UPDATEDIR.'/update%04d.sql', $v), file_exists($file); $v++) {
	    msg("MaM DB updating to $v with $file.", 2);
	    $sql = io_readFile($file, false);

	    $this->dbh->beginTransaction();
	    $res = $this->dbh->exec($sql);
	    if ($res === false) {
		 msg("MaM DB update ($file) failed: ".var_export($this->dbh->errorInfo(), true), -1);
		 $this->dbh->rollBack();
		 return false;
	    }
	    $res = $this->dbh->exec("INSERT INTO verze_db VALUES ($v, NOW());");
	    if ($res === false) {
		 msg("MaM DB version info update to ver $v failed: ".var_export($this->dbh->errorInfo(), true));
		 $this->dbh->rollBack();
		 return false;
	    }
	    $this->dbh->commit();	    
	    msg("MaM DB updated to ver $v.", 2);
	}

        return $this->dbh;
    }

    /**
     * Assumes $this->dbh set
     * @return Version of the database schema
     */
    function _getDbVersion() {
	$q = $this->dbh->query('SELECT verze FROM verze_db LIMIT 1 ORDER BY verze DESC;');
	if ($q === false) {
	    return 0;
	}
	$res = $q->fetch();
	if ($res === false) 
	    return 0;
        return $res[0];
    }

}
