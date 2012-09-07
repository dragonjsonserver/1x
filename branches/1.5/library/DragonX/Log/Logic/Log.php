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
 * Logik zur Speicherung von Logeinträgen
 */
class DragonX_Log_Logic_Log
{
    /**
     * Speichert einen neuen Logeintrag
     * @param string $method
     * @param array $params
     * @throws Zend_Log_Exception
     */
    public function __call($method, $params)
    {
    	if (!Zend_Registry::isRegistered('Zend_Log')) {
    		return;
    	}
        switch (count($params)) {
            case 0:
                throw new Zend_Log_Exception('Missing log message');
            case 1:
                $message = array_shift($params);
                $extras = null;
                break;
            default:
                $message = array_shift($params);
                $extras['params'] = Zend_Json::encode(array_shift($params));
                break;
        }
        $extras['timestamp'] = time();
        $logger = Zend_Registry::get('Zend_Log');
        $logger->__call($method, array($message, $extras));
    }
}
