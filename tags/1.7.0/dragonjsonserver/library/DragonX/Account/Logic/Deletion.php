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
 * Logikklasse zur Verwaltung von Accountlöschungen
 */
class DragonX_Account_Logic_Deletion
{
    /**
     * Setzt den Löschstatus des Accounts sodass dieser gelöscht werden kann
     * @param Application_Account_Record_Account $recordAccount
     */
    public function deleteAccount(Application_Account_Record_Account $recordAccount)
    {
        Zend_Registry::get('DragonX_Storage_Engine')->save(
            new DragonX_Account_Record_Deletion(array(
                'accountid' => $recordAccount->id,
            ))
        );
    }

    /**
     * Gibt des Löschstatus des Accounts zurück
     * @param Application_Account_Record_Account $recordAccount
     * @return DragonX_Account_Record_Deletion|null
     */
    public function getDeletion(Application_Account_Record_Account $recordAccount)
    {
        $listDeletions = Zend_Registry::get('DragonX_Storage_Engine')->loadByConditions(
            new DragonX_Account_Record_Deletion(),
            array('accountid' => $recordAccount->id)
        );
        if (count($listDeletions) == 0) {
            return;
        }
        return $listDeletions[0];
    }

    /**
     * Setzt den Löschstatus des Accounts zurück
     * @param Application_Account_Record_Account $recordAccount
     */
    public function deleteDeletion(Application_Account_Record_Account $recordAccount)
    {
        Zend_Registry::get('DragonX_Storage_Engine')->deleteByConditions(
            new DragonX_Account_Record_Deletion(),
            array('accountid' => $recordAccount->id)
        );
    }
}
