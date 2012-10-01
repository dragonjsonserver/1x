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
	 * @var array
	 */
	private $_listPackages = array();

    /**
     * Initialisiert die Struktur der Paketliste in der Datenbank
     * @param string $storagekey
     */
	private function _install($storagekey)
	{
	    $storage = Zend_Registry::get($storagekey);
        $storage->executeSqlStatement(
              "CREATE TABLE IF NOT EXISTS `dragonx_storage_record_package` ("
                . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                . "`created` INT(10) UNSIGNED NOT NULL, "
                . "`modified` INT(10) UNSIGNED NOT NULL, "
                . "`packagenamespace` VARCHAR(255) NOT NULL, "
                . "`packagename` VARCHAR(255) NOT NULL, "
                . "`version` VARCHAR(255) NOT NULL, "
                . "PRIMARY KEY (`id`),"
                . "UNIQUE KEY (`packagenamespace`, `packagename`)"
            . ") ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );
        try {
            $storage->executeSqlStatement(
                  "ALTER TABLE `dragonx_storage_record_package` "
                    . "ADD `created` INT(10) UNSIGNED NOT NULL AFTER `id`, "
                    . "ADD `modified` INT(10) UNSIGNED NOT NULL AFTER `created`"
            );
            $storage->executeSqlStatement(
                  "UPDATE `dragonx_storage_record_package` SET `created` = UNIX_TIMESTAMP(NOW()), `modified` = UNIX_TIMESTAMP(NOW())"
            );
        } catch (Exception $exception) {
        }
        try {
            $storage->executeSqlStatement(
                  "INSERT INTO `dragonx_storage_record_package` (`packagenamespace`, `packagename`, `version`) "
                . "SELECT `packagenamespace`, `packagename`, `version` FROM `dragonx_database_packages`"
            );
            $storage->executeSqlStatement("DROP TABLE `dragonx_database_packages`");
        } catch (Exception $exception) {
        }
	}

	/**
	 * Gibt die Paketliste der jeweiligen Datenbank zurück
	 * @param string $storagekey
	 * @return array
	 */
	private function _getListPackages($storagekey)
	{
        if (!isset($this->_listPackages[$storagekey])) {
            $this->_install($storagekey);
	        $this->_listPackages[$storagekey] = Zend_Registry::get($storagekey)
	            ->loadByConditions(new DragonX_Storage_Record_Package())
	            ->indexBy(array('packagenamespace', 'packagename'));
        }
        return $this->_listPackages[$storagekey];
	}

    /**
     * Installiert mit den Plugins die Packages und trägt die Versionen ein
     */
    public function installPackages()
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');
        $listPackages = $this->_getListPackages('DragonX_Storage_Engine');

        $pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
        $plugins = $pluginregistry->getPlugins('DragonX_Storage_Plugin_Install_Interface');
        $storagekeysqlstatements = array();
        foreach ($plugins as $plugin) {
            $storagekey = 'DragonX_Storage_Engine';
            if ($plugin instanceof DragonX_Storage_Plugin_GetStoragekey_Interface) {
                $storagekey = $plugin->getStoragekey();
            }
            $listPackages = $this->_getListPackages($storagekey);
            list ($packagenamespace, $packagename) = explode('_', get_class($plugin), 3);
            $version = '0.0.0';
            if (isset($listPackages[$packagenamespace]) && isset($listPackages[$packagenamespace][$packagename])) {
                list($recordPackage) = $listPackages[$packagenamespace][$packagename];
            	$version = $recordPackage->version;
            }
            if (!isset($storagekeysqlstatements[$storagekey])) {
            	$storagekeysqlstatements[$storagekey] = array();
            }
            $storagekeysqlstatements[$storagekey] = array_merge($storagekeysqlstatements[$storagekey], $plugin->getInstall($version));
        }
        foreach ($storagekeysqlstatements as $storagekey => $sqlstatements) {
        	$storage = Zend_Registry::get($storagekey);
        	foreach ($sqlstatements as $sqlstatement) {
        	    $storage->executeSqlStatement($sqlstatement);
        	}
        }

        $configEngine = new Dragon_Application_Config('dragonx/storage/engine');
        $storagekeys = array();
        if (isset($configEngine->engine)) {
            $storagekeys[] = 'DragonX_Storage_Engine';
        } else {
            foreach (array_keys($configEngine->toArray()) as $storagekey) {
            	$storage = Zend_Registry::get($storagekey);
                if ($storage instanceof DragonX_Storage_Engine_SqlStatement_Interface) {
                	$storagekeys[] = $storagekey;
                }
            }
        }

        $packagenamespaces = Zend_Registry::get('Dragon_Package_Registry')->getPackagenamespaces();
        foreach ($packagenamespaces as $packagenamespace => $packagenames) {
            foreach ($packagenames as $packagekey => $packagevalue) {
            	foreach ($storagekeys as $storagekey) {
	                if (is_int($packagekey)) {
	                    $packagename = $packagevalue;
	                } else {
	                    $packagename = $packagekey;
	                }
	                $classname = $packagenamespace . '_' . $packagename . '_Version';
	                $version = new $classname();
                    $listPackages = $this->_getListPackages($storagekey);
	                if (isset($listPackages[$packagenamespace]) && isset($listPackages[$packagenamespace][$packagename])) {
	                    list($recordPackage) = $listPackages[$packagenamespace][$packagename];
	                    $version = $version->getVersion();
	                    if ($recordPackage->version == $version) {
	                    	continue;
	                    }
	                	$recordPackage->version = $version;
	                } else {
	                	$recordPackage = new DragonX_Storage_Record_Package(array(
	                	    'packagenamespace' => $packagenamespace,
	                	    'packagename' => $packagename,
	                	    'version' => $version->getVersion(),
	                	));
	                }
                	Zend_Registry::get($storagekey)->save($recordPackage);
                }
            }
        }
        return $this;
    }
}
