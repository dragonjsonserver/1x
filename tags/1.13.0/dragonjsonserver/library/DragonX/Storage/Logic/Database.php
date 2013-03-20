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
    private $_listPlugins = array();

    /**
     * Initialisiert die Struktur der Paketliste in der Datenbank
     * @param string $storagekey
     */
    private function _install($storagekey)
    {
        $storage = Zend_Registry::get($storagekey);
        $storage->executeSqlStatement(
              "CREATE TABLE IF NOT EXISTS `dragonx_storage_record_plugin` ("
                . "`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, "
                . "`created` INT(10) UNSIGNED NOT NULL, "
                . "`modified` INT(10) UNSIGNED NOT NULL, "
                . "`pluginname` VARCHAR(255) NOT NULL, "
                . "`version` VARCHAR(255) NOT NULL, "
                . "PRIMARY KEY (`id`),"
                . "UNIQUE KEY (`pluginname`)"
            . ") ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );
    }

    /**
     * Gibt die Pluginliste der jeweiligen Datenbank zurück
     * @param string $storagekey
     * @return array
     */
    private function _getListPlugins($storagekey)
    {
        if (!isset($this->_listPlugins[$storagekey])) {
            $this->_install($storagekey);
            $this->_listPlugins[$storagekey] = Zend_Registry::get($storagekey)
                ->loadByConditions(new DragonX_Storage_Record_Plugin())
                ->indexBy('pluginname', true);
        }
        return $this->_listPlugins[$storagekey];
    }

    /**
     * Installiert mit den Plugins die Packages und trägt die Versionen ein
     * @param array $storagekeys
     */
    public function installPackages($storagekeys = null)
    {
        $plugins = Zend_Registry::get('Dragon_Plugin_Registry')->getPlugins('DragonX_Storage_Plugin_Install_Interface');
        foreach ($plugins as $plugin) {
            $storagekey = 'DragonX_Storage_Engine';
            if ($plugin instanceof DragonX_Storage_Plugin_GetStoragekey_Interface) {
                $storagekey = $plugin->getStoragekey();
            }
            if (isset($storagekeys) && !in_array($storagekey, $storagekeys)) {
                continue;
            }
            $listPlugins = $this->_getListPlugins($storagekey);
            $pluginname = get_class($plugin);
            $version = '0.0.0';
            if (isset($listPlugins[$pluginname])) {
                $version = $listPlugins[$pluginname]->version;
            }
            $storage = Zend_Registry::get($storagekey);
            $storage->beginTransaction();
            try {
                $plugin->install($storage, $version);
                list ($packagenamespace, $packagename) = explode('_', $pluginname, 3);
                $classname = $packagenamespace . '_' . $packagename . '_Version';
                $version = new $classname();
                if (isset($listPlugins[$pluginname])) {
                    $version = $version->getVersion();
                    if ($listPlugins[$pluginname]->version != $version) {
                        $listPlugins[$pluginname]->version = $version;
                        $storage->save($listPlugins[$pluginname]);
                    }
                } else {
                    $storage->save(new DragonX_Storage_Record_Plugin(array(
                        'pluginname' => $pluginname,
                        'version' => $version->getVersion(),
                    )));
                }
                $storage->commit();
            } catch (Exception $exception) {
                $storage->rollback();
                throw $exception;
            }
        }
        return $this;
    }
}
