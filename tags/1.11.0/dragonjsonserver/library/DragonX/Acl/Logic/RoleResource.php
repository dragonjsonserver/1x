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
 * Logikklasse zur Verwaltung der Zuordnungen von Rolle zu Ressource
 */
class DragonX_Acl_Logic_RoleResource
{
    /**
     * Fügt eine neue Zuordnung von einer Rolle zu einer Ressource hinzu
     * @param integer $role_id
     * @param integer $resource_id
     */
    public function addRoleResource($role_id, $resource_id)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        $storage->load(new DragonX_Acl_Record_Role($role_id));
        $storage->load(new DragonX_Acl_Record_Resource($resource_id));
        $storage->save(
            new DragonX_Acl_Record_RoleResource(array('role_id' => $role_id, 'resource_id' => $resource_id))
        );
    }

    /**
     * Entfernt eine Zuordnung von einer Rolle zu einer Ressource
     * @param integer $roleresource_id
     */
    public function removeRoleResource($roleresource_id)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        $storage->delete(
            new DragonX_Acl_Record_RoleResource($roleresource_id)
        );
    }
}
