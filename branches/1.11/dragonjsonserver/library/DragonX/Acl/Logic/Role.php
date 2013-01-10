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
 * Logikklasse zur Verwaltung der Rollen
 */
class DragonX_Acl_Logic_Role
{
    /**
     * Fügt eine neue Rolle hinzu
     * @param string $name
     * @param integer $parentrole_id
     */
    public function addRole($name, $parentrole_id = null)
    {
        $recordRoleParent = null;
        if (isset($parentrole_id)) {
            $recordRoleParent = new DragonX_Acl_Record_Role($parentrole_id);
        }
        $logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
        $logicNestedSet->addNode(
            new DragonX_Acl_Record_Role(array('name' => $name)),
            $recordRoleParent
        );
    }

    /**
     * Entfernt eine Rolle samt untergeordneten Rollen
     * @param integer $role_id
     */
    public function removeRole($role_id)
    {
        $logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
        $logicNestedSet->removeNode(
            new DragonX_Acl_Record_Role($role_id)
        );
    }

    /**
     * Benennt eine Rolle um
     * @param integer $role_id
     * @param string $name
     */
    public function renameRole($role_id, $name)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        $recordRole = $storage->load(new DragonX_Acl_Record_Role($role_id));
        $recordRole->name = $name;
        $storage->save($recordRole);
    }
}
