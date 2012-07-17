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
 * Logikklasse für die Datenbank
 */
class DragonX_Database_Logic_Database extends DragonX_Database_Logic_Abstract
{
	/**
	 * Installiert mit den Plugins die Packages und trägt die Versionen ein
	 */
    public function installPackages()
    {
        $databasemodel = new DragonX_Database_Model_Database();
        $databasemodel->createPackageTable();

    	$pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
        $plugins = $pluginregistry->getPlugins('DragonX_Database_Plugin_Install_Interface');
        $sqls = array();
        foreach ($plugins as $plugin) {
	        list ($packagenamespace, $packagename) = explode('_', get_class($plugin), 3);;
        	$rows = $databasemodel->selectPackage($packagenamespace, $packagename);
        	$version = '0.0.0';
        	if (isset($rows[0]['version'])) {
        		$version = $rows[0]['version'];
        	}
            $sqls = array_merge($sqls, $plugin->getInstall($version));
        }
        $databasemodel->installPackages($sqls);

        $packagenamespaces = Zend_Registry::get('Dragon_Package_Registry')->getPackagenamespaces();
        foreach ($packagenamespaces as $packagenamespace => $packagenames) {
        	foreach ($packagenames as $packagekey => $packagevalue) {
            	if (is_int($packagekey)) {
            		$packagename = $packagevalue;
            	} else {
            		$packagename = $packagekey;
            	}
                $classname = $packagenamespace . '_' . $packagename . '_Version';
                $version = new $classname();
                $databasemodel->insertupdatePackage($packagenamespace, $packagename, $version->getVersion());
            }
        }
        return $this;
    }
}
