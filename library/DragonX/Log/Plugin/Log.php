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
 * Plugin zur Initialisierung des Zend_Log Objektes
 */
class DragonX_Log_Plugin_Log implements Dragon_Application_Plugin_Bootstrap_Interface
{
    /**
     * Initialisiert bei jedem Request das Zend_Log Objekt
     */
    public function bootstrap()
    {
        $configLog = new Dragon_Application_Config('dragonx/log/log');
        $logger = new Zend_Log();
        foreach ($configLog->eventitems as $name => $value) {
            $logger->setEventItem($name, $value);
        }
        foreach ($configLog->writers as $writer) {
            $logger->addWriter($writer);
        }
        foreach ($configLog->filters as $filter) {
            $logger->addFilter($filter);
        }
        Zend_Registry::set('Zend_Log', $logger);
    }
}
