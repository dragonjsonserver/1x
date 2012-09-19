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
 * Plugin zur Initialisierung der Storage Engine bei jedem Request
 */
class DragonX_Storage_Plugin_Storage implements Dragon_Application_Plugin_Bootstrap_Interface
{
    /**
     * Initialisiert bei jedem Request die Storage Engine
     */
    public function bootstrap()
    {
        $configEngine = new Dragon_Application_Config('dragonx/storage/engine');
        if (isset($configEngine->adapter)) {
        	Zend_Registry::set('DragonX_Storage_Engine', $configEngine->engine);
        } else {
            $configEngines = $configEngine;
            foreach ($configEngines as $key => $configEngine) {
                Zend_Registry::set($key, $configEngine->engine);
            }
        }
    }
}
