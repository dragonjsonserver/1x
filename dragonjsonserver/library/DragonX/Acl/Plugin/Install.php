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
class DragonX_Acl_Plugin_Install implements DragonX_Storage_Plugin_Install_Interface
{
    /**
     * Gibt die SQL Statements zurück um das Paket zu updaten
     * @param string $oldversion
     * @return array
     */
    public function getInstall($oldversion = '0.0.0')
    {
        $sqlstatements = array();
        if (version_compare($oldversion, '1.3.0', '<')) {
            $sqlstatements[] =
                  "CREATE TABLE `dragonx_acl_record_resource` ("
			        . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
			        . "`name` VARCHAR(255) NOT NULL, "
			        . "`lft` INT(10) UNSIGNED NOT NULL, "
			        . "`rgt` INT(10) UNSIGNED NOT NULL, "
			        . "PRIMARY KEY (`id`), "
			        . "KEY (`lft`), "
			        . "KEY (`rgt`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $sqlstatements[] =
                  "INSERT INTO `dragonx_acl_record_resource` (`name`, `lft`, `rgt`) "
                . "VALUES ('All', 1, 2)";

            $sqlstatements[] =
                  "CREATE TABLE `dragonx_acl_record_role` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`name` VARCHAR(255) NOT NULL, "
                    . "`lft` INT(10) UNSIGNED NOT NULL, "
                    . "`rgt` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "KEY (`lft`), "
                    . "KEY (`rgt`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $sqlstatements[] =
                  "INSERT INTO `dragonx_acl_record_role` (`name`, `lft`, `rgt`) "
                . "VALUES ('All', 1, 2)";

            $sqlstatements[] =
                  "CREATE TABLE `dragonx_acl_record_roleresource` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`roleid` INT(10) UNSIGNED NOT NULL, "
                    . "`resourceid` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY (`roleid`, `resourceid`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $sqlstatements[] =
                  "CREATE TABLE `dragonx_acl_record_accountrole` ("
                    . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`accountid` INT(10) UNSIGNED NOT NULL, "
                    . "`roleid` INT(10) UNSIGNED NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY (`accountid`, `roleid`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }
        if (version_compare($oldversion, '1.8.0', '<')) {
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_acl_record_accountrole` "
                    . "CHANGE `accountid` `account_id` INT(10) UNSIGNED NOT NULL, "
                    . "CHANGE `roleid` `role_id` INT(10) UNSIGNED NOT NULL";
            $sqlstatements[] =
                  "ALTER TABLE `dragonx_acl_record_roleresource` "
                    . "CHANGE `roleid` `role_id` INT(10) UNSIGNED NOT NULL, "
                    . "CHANGE `resourceid` `resource_id` INT(10) UNSIGNED NOT NULL";
        }
        return $sqlstatements;
    }
}
