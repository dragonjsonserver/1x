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
class DragonX_Account_Plugin_Install
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
                  "CREATE TABLE `dragonx_account_accounts` ("
                    . "`accountid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`identity` VARCHAR(255) NOT NULL, "
                    . "`credential` CHAR(32) NOT NULL, "
                    . "PRIMARY KEY (`accountid`), "
                    . "UNIQUE KEY `identity` (`identity`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        if (version_compare($oldversion, '1.2.0', '<')) {
            $sqlstatements[] =
                  "CREATE TABLE `dragonx_account_record_account` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`identity` VARCHAR(255) NOT NULL, "
                    . "`credential` CHAR(32) NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY (`identity`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $sqlstatements[] =
                  "INSERT INTO `dragonx_account_record_account` (`id`, `identity`, `credential`) "
                . "SELECT `accountid`, `identity`, `credential` FROM `dragonx_account_accounts`";
            $sqlstatements[] = "DROP TABLE `dragonx_account_accounts`";

            $sqlstatements[] =
                  "CREATE TABLE `dragonx_account_record_credential` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`accountid` INT(10) UNSIGNED NOT NULL, "
                    . "`credentialhash` CHAR(32) NOT NULL, "
                    . "`timestamp` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY `credentialhash` (`credentialhash`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        return $sqlstatements;
    }
}
