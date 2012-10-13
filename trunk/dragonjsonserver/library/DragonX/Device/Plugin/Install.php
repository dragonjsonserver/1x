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
class DragonX_Device_Plugin_Install implements DragonX_Storage_Plugin_Install_Interface
{
    /**
     * Gibt die SQL Statements zurück um das Paket zu updaten
     * @param string $oldversion
     * @return array
     */
    public function getInstall($oldversion = '0.0.0')
    {
        $sqlstatements = array();
        if (version_compare($oldversion, '1.7.0', '<')) {
            $sqlstatements[] =
                  "CREATE TABLE `dragonx_device_record_device` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`created` INT(10) UNSIGNED NOT NULL, "
                    . "`modified` INT(10) UNSIGNED NOT NULL, "
                    . "`accountid` INT(10) UNSIGNED NOT NULL, "
                    . "`platform` VARCHAR(255) NOT NULL, "
                    . "`name` VARCHAR(255) NOT NULL, "
                    . "`pushnotification_platform` VARCHAR(255) NOT NULL, "
                    . "`pushnotification_token` VARCHAR(255) NOT NULL, "
                    . "PRIMARY KEY (`id`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        return $sqlstatements;
    }
}
