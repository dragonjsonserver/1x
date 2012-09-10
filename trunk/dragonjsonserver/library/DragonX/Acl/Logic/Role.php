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
     * @param integer $parentroleid
     */
    public function addRole($name, $parentroleid)
    {
    	$logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
    	$logicNestedSet->addNode(
    	    new DragonX_Acl_Record_Role(array('name' => $name)),
    	    new DragonX_Acl_Record_Role($parentroleid)
    	);
    }

    /**
     * Entfernt eine Rolle samt untergeordneten Rollen
     * @param integer $roleid
     */
    public function removeRole($roleid)
    {
        $logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
        $logicNestedSet->removeNode(
            new DragonX_Acl_Record_Role($roleid)
        );
    }

    /**
     * Benennt eine Rolle um
     * @param integer $roleid
     * @param string $name
     */
    public function renameRole($roleid, $name)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        if (!$recordRole = $storage->load(new DragonX_Acl_Record_Role($roleid))) {
        	throw Exception('missing role');
        }
        $recordRole->name = $name;
        $storage->save($recordRole);
    }
}
