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
 * Log Writer zur Speicherung von Logeinträgen
 */
class DragonX_Log_Writer_Storage extends Zend_Log_Writer_Abstract
{
    /**
     * Speichert den Logeintrag im Storage
     * @param array $event
     */
    protected function _write($event)
    {
    	$recordLog = new DragonX_Log_Record_Log($event);
    	Zend_Registry::get('DragonX_Storage_Engine')->saveRecord($recordLog);
    }

    /**
     * Erstellt eine neue Instanz des Log Writers
     * @param array|Zend_Config $config
     * @return DragonX_Log_Writer_Storage
     */
    static public function factory($config)
    {
        return new self();
    }
}
