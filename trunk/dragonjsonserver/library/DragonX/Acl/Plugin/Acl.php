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
 * Plugin zur Abfrage der Benutzerrechte bei jedem Request
 */
class DragonX_Acl_Plugin_Acl
    implements Dragon_Json_Plugin_PreDispatch_Interface
{
    /**
     * Prüft bei jedem Request ist die Authentifizierung
     * @param Dragon_Json_Server_Request_Http $request
     */
    public function preDispatch(Dragon_Json_Server_Request_Http $request)
    {
    	try {
            list ($classname, $methodname) = $request->parseMethod();
            $reflectionClass = new Zend_Reflection_Class($classname);
            $tagResource = $reflectionClass->getMethod($methodname)->getDocblock()->getTag('dragonx_acl_resource');
	        if (!$tagResource) {
	            return;
	        }
            $resource = $tagResource->getDescription();
        } catch (Exception $exception) {
	        return;
        }
        $resources = array();
        if (Zend_Registry::isRegistered('recordAccount')) {
	        $logicAcl = new DragonX_Acl_Logic_Acl();
        	$resources = $logicAcl->getResources(Zend_Registry::get('recordAccount'));
        }
        if (!in_array($resource, $resources)) {
        	throw new Exception('missing resource ' . $resource);
        }
    }
}
