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
     * @param integer $parentresource_id
     */
    public function addResource($name, $parentresource_id)
    {
        $logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
        $logicNestedSet->addNode(
            new DragonX_Acl_Record_Resource(array('name' => $name)),
            new DragonX_Acl_Record_Resource($parentresource_id)
        );
    }

    /**
     * Entfernt eine Ressource samt untergeordneten Ressourcen
     * @param integer $resource_id
     */
    public function removeResource($resource_id)
    {
        $logicNestedSet = new DragonX_NestedSet_Logic_NestedSet();
        $logicNestedSet->removeNode(
            new DragonX_Acl_Record_Resource($resource_id)
        );
    }
}
