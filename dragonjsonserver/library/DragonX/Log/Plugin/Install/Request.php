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
 * Plugin zur Installation des Paketes
 */
class DragonX_Log_Plugin_Install_Request
	implements DragonX_Storage_Plugin_Install_Interface,
	           DragonX_Storage_Plugin_GetStoragekey_Interface
{
    /**
     * Gibt den Key der Storage Engine in der Zend Registry zurück
     * @return string
     */
    public function getStoragekey()
    {
    	$configEngine = new Dragon_Application_Config('dragonx/log/engine');
    	return $configEngine->engine;
    }

    /**
     * Installiert das Plugin in der übergebenen Datenbank
     * @param DragonX_Storage_Engine_ZendDbAdataper $storage
     * @param string $version
     */
    public function install(DragonX_Storage_Engine_ZendDbAdataper $storage, $version = '0.0.0')
    {
        if (version_compare($version, '1.8.0', '<')) {
            $storage->executeSqlStatement(
                  "CREATE TABLE `dragonx_log_record_request` ("
                    . "`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`rpcmethod` VARCHAR(255) NOT NULL, "
                    . "`rpcid` VARCHAR(255) NOT NULL, "
                    . "`rpcversion` VARCHAR(255) NOT NULL, "
                    . "`requestparams` TEXT NOT NULL, "
                    . "`requesttimestamp` INT(10) UNSIGNED NOT NULL, "
                    . "`response` TEXT NULL, "
                    . "`responsetimestamp` INT(10) UNSIGNED NULL, "
                    . "PRIMARY KEY (`id`) "
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8"
            );
        }
    }
}
