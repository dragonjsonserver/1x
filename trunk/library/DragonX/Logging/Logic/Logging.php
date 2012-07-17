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
 * Logik zur Speicherung von Log- und Fehlereinträgen
 */
class DragonX_Logging_Logic_Logging extends DragonX_Database_Logic_Abstract
{
    /**
     * Speichert einen neuen Logeintrag
     * @param string $logtype
     * @param array $params
     */
    public function log($logtype, array $params = array())
    {
        $modelLogging = new DragonX_Logging_Model_Logging();
        $modelLogging->log(DragonX_Logging_Logic_Request::getRequestID(), $logtype, Zend_Json::encode($params), time());
    }

    /**
     * Speichert einen neuen Fehlereintrag
     * @param string $errortype
     * @param array $params
     */
    public function error($errortype, array $params = array())
    {
        $modelLogging = new DragonX_Logging_Model_Logging();
        $modelLogging->error(DragonX_Logging_Logic_Request::getRequestID(), $errortype, Zend_Json::encode($params), time());
    }
}
