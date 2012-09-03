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
 * Logikklasse zur Verwaltung der Ressourcen
 */
class DragonX_Acl_Logic_Resource
{
    /**
     * Fügt eine neue Ressource hinzu
     * @param string $name
     * @param integer $parentresourceid
     */
    public function addResource($name, $parentresourceid)
    {
        $logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
        $logicNestedSet->addNode(
            new DragonX_Acl_Record_Resource(array('name' => $name)),
            new DragonX_Acl_Record_Resource($parentresourceid)
        );
    }

    /**
     * Entfernt eine Ressource samt untergeordneten Ressourcen
     * @param integer $resourceid
     */
    public function removeResource($resourceid)
    {
        $logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
        $logicNestedSet->removeNode(
            new DragonX_Acl_Record_Resource($resourceid)
        );
    }
}
