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
 * Plugin zur Löschung aller verknüpften Daten zu einem Account
 */
class DragonX_Device_Plugin_DeleteAccount
    implements DragonX_Account_Plugin_DeleteAccount_Interface
{
    /**
     * Wird vor der Löschung eines Accounts aufgerufen
     * @param Application_Account_Record_Account $recordAccount
     */
    public function deleteAccount(Application_Account_Record_Account $recordAccount)
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');
        $storage->deleteByConditions(
            new DragonX_Device_Record_Device(),
            array('account_id' => $recordAccount->id)
        );
        $storage->executeSqlStatement(
            "DELETE FROM dragonx_device_record_credential WHERE device_id NOT IN (
                SELECT id FROM dragonx_device_record_device
            )"
        );
    }
}
