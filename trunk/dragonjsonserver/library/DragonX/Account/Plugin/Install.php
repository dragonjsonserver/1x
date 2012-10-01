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
class DragonX_Account_Plugin_Install implements DragonX_Storage_Plugin_Install_Interface
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
        if (version_compare($oldversion, '1.3.0', '<')) {
            $sqlstatements[] =
                  "CREATE TABLE `dragonx_account_record_validation` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`accountid` INT(10) UNSIGNED NOT NULL, "
                    . "`validationhash` CHAR(32) NOT NULL, "
                    . "`timestamp` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY `validationhash` (`validationhash`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $sqlstatements[] =
                  "CREATE TABLE `dragonx_account_record_deletion` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`accountid` INT(10) UNSIGNED NOT NULL, "
                    . "`timestamp` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY `accountid` (`accountid`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        if (version_compare($oldversion, '1.4.0', '<')) {
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_account_record_account` "
                    . "CHANGE `identity` `identity` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, "
                    . "CHANGE `credential` `credential` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL";
        }
        if (version_compare($oldversion, '1.5.0', '<')) {
            $sqlstatements[] =
                  "CREATE TABLE `dragonx_account_record_session` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`accountid` INT(10) UNSIGNED NOT NULL, "
                    . "`sessionhash` CHAR(32) NOT NULL, "
                    . "`timestamp` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY `sessionhash` (`sessionhash`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        if (version_compare($oldversion, '1.7.0', '<')) {
            if (Zend_Registry::get('Dragon_Package_Registry')->isAvailable('Application', 'Account')) {
                $installAccount = new Application_Account_Plugin_Install();
                $sqlstatements = array_merge($sqlstatements, $installAccount->getInstall());

                $sqlstatements[] =
                      "INSERT INTO `application_account_record_account` (`id`, `created`, `modified`) "
                    . "SELECT `id`, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()) FROM `dragonx_account_record_account`";
            }
        	if (Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Emailaddress')) {
                $installEmailaddress = new DragonX_Emailaddress_Plugin_Install();
                $sqlstatements = array_merge($sqlstatements, $installEmailaddress->getInstall());

                $sqlstatements[] =
                      "INSERT INTO `dragonx_emailaddress_record_emailaddress` (`created`, `modified`, `accountid`, `emailaddress`, `passwordhash`) "
                    . "SELECT UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), `id`, `identity`, `credential` FROM `dragonx_account_record_account` WHERE `identity` IS NOT NULL";

                $sqlstatements[] =
                      "INSERT INTO `dragonx_emailaddress_record_credential` (`created`, `emailaddressid`, `credentialhash`) "
                    . "SELECT `dragonx_account_record_credential`.`timestamp`, `dragonx_emailaddress_record_emailaddress`.`id`, `dragonx_account_record_credential`.`credentialhash` "
                    . "FROM `dragonx_account_record_credential` "
                    . "INNER JOIN `dragonx_emailaddress_record_emailaddress` ON `dragonx_emailaddress_record_emailaddress`.`accountid` = `dragonx_account_record_credential`.`accountid`";

                $sqlstatements[] =
                      "INSERT INTO `dragonx_emailaddress_record_validation` (`created`, `emailaddressid`, `validationhash`) "
                    . "SELECT `dragonx_account_record_validation`.`timestamp`, `dragonx_emailaddress_record_emailaddress`.`id`, `dragonx_account_record_validation`.`validationhash` "
                    . "FROM `dragonx_account_record_validation` "
                    . "INNER JOIN `dragonx_emailaddress_record_emailaddress` ON `dragonx_emailaddress_record_emailaddress`.`accountid` = `dragonx_account_record_validation`.`accountid`";
        	}
            $sqlstatements[] = "DROP TABLE `dragonx_account_record_account`";

            $sqlstatements[] =
                  "ALTER TABLE `dragonx_account_record_deletion` "
                    . "ADD `created` INT(10) UNSIGNED NOT NULL AFTER `id`";
            $sqlstatements[] = "UPDATE `dragonx_account_record_deletion` SET `created` = `timestamp`";
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_account_record_deletion` "
                    . "DROP `timestamp`";

            $sqlstatements[] =
                  "ALTER TABLE `dragonx_account_record_session` "
                    . "ADD `created` INT(10) UNSIGNED NOT NULL AFTER `id`";
            $sqlstatements[] = "UPDATE `dragonx_account_record_session` SET `created` = `timestamp`";
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_account_record_session` "
                    . "DROP `timestamp`";

            $sqlstatements[] = "DROP TABLE `dragonx_account_record_credential`";
            $sqlstatements[] = "DROP TABLE `dragonx_account_record_validation`";
        }
        return $sqlstatements;
    }
}
