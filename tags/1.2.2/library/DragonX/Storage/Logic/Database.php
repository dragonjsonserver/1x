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
class DragonX_Storage_Logic_Database
{
    /**
     * Installiert mit den Plugins die Packages und trägt die Versionen ein
     */
    public function installPackages()
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');

    	$storage->executeSqlStatement(
              "CREATE TABLE IF NOT EXISTS `dragonx_storage_record_package` ("
                . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                . "`packagenamespace` VARCHAR(255) NOT NULL, "
                . "`packagename` VARCHAR(255) NOT NULL, "
                . "`version` VARCHAR(255) NOT NULL, "
                . "PRIMARY KEY (`id`),"
                . "UNIQUE KEY (`packagenamespace`, `packagename`)"
            . ") ENGINE=InnoDB DEFAULT CHARSET=utf8"
    	);
    	try {
            $storage->executeSqlStatement(
                  "INSERT INTO `dragonx_storage_record_package` (`packagenamespace`, `packagename`, `version`) "
                . "SELECT `packagenamespace`, `packagename`, `version` FROM `dragonx_database_packages`"
            );
            $storage->executeSqlStatement("DROP TABLE `dragonx_database_packages`");
    	} catch (Exception $exception) {
    	}
        $listPackages = $storage
            ->loadByConditions(new DragonX_Storage_Record_Package())
            ->indexBy(array('packagenamespace', 'packagename'));

        $pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
        $plugins = $pluginregistry->getPlugins('DragonX_Storage_Plugin_Install_Interface');
        $sqlstatements = array();
        foreach ($plugins as $plugin) {
            list ($packagenamespace, $packagename) = explode('_', get_class($plugin), 3);;
            $version = '0.0.0';
            if (isset($listPackages[$packagenamespace]) && isset($listPackages[$packagenamespace][$packagename])) {
                list($recordPackage) = $listPackages[$packagenamespace][$packagename];
            	$version = $recordPackage->version;
            }
            $sqlstatements = array_merge($sqlstatements, $plugin->getInstall($version));
        }
        foreach ($sqlstatements as $sqlstatement) {
        	$storage->executeSqlStatement($sqlstatement);
        }

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
                if (isset($listPackages[$packagenamespace]) && isset($listPackages[$packagenamespace][$packagename])) {
                    list($recordPackage) = $listPackages[$packagenamespace][$packagename];
                	$recordPackage->version = $version->getVersion();
                } else {
                	$recordPackage = new DragonX_Storage_Record_Package(array(
                	    'packagenamespace' => $packagenamespace,
                	    'packagename' => $packagename,
                	    'version' => $version->getVersion(),
                	));
                }
                $storage->save($recordPackage);
            }
        }
        return $this;
    }
}
