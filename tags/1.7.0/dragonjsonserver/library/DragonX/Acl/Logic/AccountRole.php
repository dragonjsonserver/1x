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
 * Logikklasse zur Verwaltung der Zuordnungen von Accounts zu Rollen
 */
class DragonX_Acl_Logic_AccountRole
{
    /**
     * Fügt eine neue Zuordnung von einem Account zu einer Rolle hinzu
     * @param integer $accountid
     * @param integer $roleid
     */
    public function addAccountRole($accountid, $roleid)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        $storage->load(new Application_Account_Record_Account($accountid));
        $storage->load(new DragonX_Acl_Record_Role($roleid));
        $storage->save(
            new DragonX_Acl_Record_AccountRole(array('accountid' => $accountid, 'roleid' => $roleid))
        );
    }

    /**
     * Entfernt eine Zuordnung von einem Account zu einer Rolle
     * @param integer $accountroleid
     */
    public function removeAccountRole($accountroleid)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        $storage->delete(
            new DragonX_Acl_Record_AccountRole($accountroleid)
        );
    }
}
