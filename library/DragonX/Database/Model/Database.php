<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled with this
 * package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL: http://dragonjsonserver.de/license. If you did
 * not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@dragonjsonserver.de. So we
 * can send you a copy immediately.
 *
 * @copyright Copyright (c) 2012 DragonProjects (http://dragonprojects.de)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @author Christoph Herrmann <developer@dragonjsonserver.de>
 */

/**
 * Modelklasse für die Datenbank
 */
class DragonX_Database_Model_Database extends DragonX_Database_Model_Abstract
{
	/**
	 * Erstellt die Tabelle für die Paketversionen wenn sie nicht existiert
	 * @return DragonX_Database_Model_Database
	 */
    public function createPackageTable()
    {
        $this->_query(
            "CREATE TABLE IF NOT EXISTS `dragonx_database_packages` ("
              . "`packagenamespace` varchar(255) NOT NULL, "
              . "`packagename` varchar(255) NOT NULL, "
              . "`version` varchar(255) NOT NULL, "
              . "PRIMARY KEY (`packagenamespace`, `packagename`)"
          . ") ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );
        return $this;
    }

    /**
     * Gibt die Versionsnummer des übergebenen Paketes zurück
     * @param string $packagenamespace
     * @param string $packagename
     * @return array
     */
    public function selectPackage($packagenamespace, $packagename)
    {
        return $this->_select(
            'dragonx_database_packages',
            array('version'),
            array('packagenamespace' => $packagenamespace, 'packagename' => $packagename)
        );
    }

    /**
     * Führt die SQL Statements zur Installation der Pakete aus
     * @param array $sqls
     * @return DragonX_Database_Model_Database
     */
    public function installPackages($sqls)
    {
        foreach ($sqls as $sql) {
            $this->_query($sql);
        }
        return $this;
    }

    /**
     * Speichert die Version für das übergebene Paket in der Datenbank
     * @param string $packagenamespace
     * @param string $packagename
     * @param string $version
     * @return DragonX_Database_Model_Database
     */
    public function insertupdatePackage($packagenamespace, $packagename, $version)
    {
    	$this->_insertupdate(
    	    'dragonx_database_packages',
    	    array('packagenamespace' => $packagenamespace, 'packagename' => $packagename, 'version' => $version)
    	);
        return $this;
    }
}
