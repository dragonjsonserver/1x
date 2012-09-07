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
     * @param integer $roleid
     * @param integer $resourceid
     */
    public function addRoleResource($roleid, $resourceid)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        if (!$storage->load(new DragonX_Acl_Record_Role($roleid))) {
            throw Exception('missing role');
        }
        if (!$storage->load(new DragonX_Acl_Record_Resource($resourceid))) {
            throw Exception('missing resource');
        }
        $storage->save(
            new DragonX_Acl_Record_RoleResource(array('roleid' => $roleid, 'resourceid' => $resourceid))
        );
    }

    /**
     * Entfernt eine Zuordnung von einer Rolle zu einer Ressource
     * @param integer $roleresourceid
     */
    public function removeRoleResource($roleresourceid)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        $storage->delete(
            new DragonX_Acl_Record_RoleResource($roleresourceid)
        );
    }
}
