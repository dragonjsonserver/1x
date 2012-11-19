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
 * Serviceklasse zur Verwaltung einer Accountlöschung
 */
class DragonX_Account_Service_Deletion
{
    /**
     * Setzt den Löschstatus des Accounts sodass dieser gelöscht werden kann
     * @dragonx_account_authenticate
     */
    public function deleteAccount()
    {
        $logicDeletion = new DragonX_Account_Logic_Deletion();
        $logicDeletion->deleteAccount(Zend_Registry::get('recordAccount'));
    }

    /**
     * Gibt des Löschstatus des Accounts zurück
     * @dragonx_account_authenticate
     * @return array
     */
    public function getDeletion()
    {
        $logicDeletion = new DragonX_Account_Logic_Deletion();
        $recordDeletion = $logicDeletion->getDeletion(Zend_Registry::get('recordAccount'));
        if (isset($recordDeletion)) {
            $configDeletion = new Dragon_Application_Config('dragonx/account/deletion');
            return array(
                'created' => $recordDeletion->created,
                'deleted' => $recordDeletion->created + $configDeletion->lifetime,
            );
        }
    }

    /**
     * Setzt den Löschstatus des Accounts zurück
     * @dragonx_account_authenticate
     */
    public function deleteDeletion()
    {
        $logicDeletion = new DragonX_Account_Logic_Deletion();
        $logicDeletion->deleteDeletion(Zend_Registry::get('recordAccount'));
    }
}
