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
class DragonX_Log_Plugin_Install
    implements DragonX_Storage_Plugin_Install_Interface
{
    /**
     * Gibt die SQL Statements zurück um das Paket zu updaten
     * @param string $oldversion
     * @return array
     */
    public function getInstall($oldversion = '0.0.0')
    {
        $sqlstatements = array();
        if (version_compare($oldversion, '1.0.0', '<')) {
            $sqlstatements[] =
                "CREATE TABLE `dragonx_log_record_request` ("
                  . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                  . "`rpcmethod` VARCHAR(255) NOT NULL, "
                  . "`rpcid` VARCHAR(255) NOT NULL, "
                  . "`rpcversion` VARCHAR(255) NOT NULL, "
                  . "`requestparams` TEXT NOT NULL, "
                  . "`requesttimestamp` INT(10) UNSIGNED NOT NULL, "
                  . "`response` TEXT NULL, "
                  . "`responsetimestamp` INT(10) UNSIGNED NULL, "
                  . "PRIMARY KEY (`id`) "
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $sqlstatements[] =
                "CREATE TABLE `dragonx_log_record_log` ("
                  . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                  . "`requestid` INT(10) NULL, "
                  . "`accountid` INT(10) NULL, "
                  . "`priority` INT(10) UNSIGNED NOT NULL, "
                  . "`message` TEXT NOT NULL, "
                  . "`params` TEXT NULL, "
                  . "`timestamp` INT(10) UNSIGNED NOT NULL, "
                  . "PRIMARY KEY (`id`) "
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        return $sqlstatements;
    }
}
