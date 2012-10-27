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
class DragonX_Clientmessage_Plugin_Install implements DragonX_Storage_Plugin_Install_Interface
{
    /**
     * Gibt die SQL Statements zurück um das Paket zu updaten
     * @param string $oldversion
     * @return array
     */
    public function getInstall($oldversion = '0.0.0')
    {
        $sqlstatements = array();
        if (version_compare($oldversion, '1.2.0', '<')) {
            $sqlstatements[] =
                  "CREATE TABLE `dragonx_clientmessage_record_account` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`accountid` INT(10) UNSIGNED NOT NULL, "
                    . "`key` VARCHAR(255) NOT NULL, "
                    . "`result` TEXT NOT NULL, "
                    . "`timestamp` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "KEY (`accountid`, `timestamp`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $sqlstatements[] =
                  "CREATE TABLE `dragonx_clientmessage_record_all` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`key` VARCHAR(255) NOT NULL, "
                    . "`result` TEXT NOT NULL, "
                    . "`timestamp` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "KEY (`timestamp`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        if (version_compare($oldversion, '1.7.0', '<')) {
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_clientmessage_record_account` "
                    . "ADD `created` INT(10) UNSIGNED NOT NULL AFTER `id`";
            $sqlstatements[] = "UPDATE `dragonx_clientmessage_record_account` SET `created` = `timestamp`";
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_clientmessage_record_account` "
                    . "DROP `timestamp`";

            $sqlstatements[] =
                  "ALTER TABLE `dragonx_clientmessage_record_all` "
                    . "ADD `created` INT(10) UNSIGNED NOT NULL AFTER `id`";
            $sqlstatements[] = "UPDATE `dragonx_clientmessage_record_all` SET `created` = `timestamp`";
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_clientmessage_record_all` "
                    . "DROP `timestamp`";
        }
        if (version_compare($oldversion, '1.8.0', '<')) {
            $sqlstatements[] = "ALTER TABLE `dragonx_clientmessage_record_account` CHANGE `accountid` `account_id` INT(10) UNSIGNED NOT NULL";
        }
        return $sqlstatements;
    }
}
