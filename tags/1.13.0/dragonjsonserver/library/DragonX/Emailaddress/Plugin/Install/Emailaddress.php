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
class DragonX_Emailaddress_Plugin_Install_Emailaddress implements DragonX_Storage_Plugin_Install_Interface
{
    /**
     * Installiert das Plugin in der übergebenen Datenbank
     * @param DragonX_Storage_Engine_ZendDbAdataper $storage
     * @param string $version
     */
    public function install(DragonX_Storage_Engine_ZendDbAdataper $storage, $version = '0.0.0')
    {
        if (version_compare($version, '1.8.0', '<')) {
            $storage->executeSqlStatement(
                  "CREATE TABLE `dragonx_emailaddress_record_emailaddress` ("
                    . "`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, "
                    . "`created` INT(10) UNSIGNED NOT NULL, "
                    . "`modified` INT(10) UNSIGNED NOT NULL, "
                    . "`account_id` BIGINT(20) UNSIGNED NOT NULL, "
                    . "`emailaddress` VARCHAR(255) NOT NULL, "
                    . "`passwordhash` CHAR(60) BINARY NOT NULL, "
                    . "PRIMARY KEY (`id`), "
                    . "UNIQUE KEY (`account_id`), "
                    . "UNIQUE KEY (`emailaddress`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8"
            );
        }
    }
}
