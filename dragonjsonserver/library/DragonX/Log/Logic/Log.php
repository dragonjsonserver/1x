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
	    $extras = null;
        switch (count($params)) {
            case 0:
                throw new Dragon_Application_Exception('missing message');
            case 1:
            	$param = array_shift($params);
            	if ($param instanceof Exception) {
            		$message = $param->getMessage();
            		$extras = array(
            			'params' => array(
	            			'code' => $param->getCode(),
	            			'file' => $param->getFile(),
	            			'line' => $param->getLine(),
	            			'trace' => $param->getTrace(),
            			)
            		);
            		if ($param instanceof Dragon_Application_Exception) {
            			$extras['params'] += array(
            				'data' => $param->getData(),
            			);
            		}
            	} else {
	                $message = $param;
            	}
                break;
            default:
                $message = array_shift($params);
                $extras = array('params' => array_shift($params));
                break;
        }
        if (isset($extras) && is_array($extras['params'])) {
        	$extras['params'] = Zend_Json::encode($extras['params']);
        }
        $logger = Zend_Registry::get('Zend_Log');
        $logger->__call($method, array($message, $extras));
    }
}
