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
 * Model zur Speicherung von Log- und Fehlereinträgen
 */
class DragonX_Logging_Model_Logging extends DragonX_Database_Model_Abstract
{
    /**
     * Speichert einen neuen Logeintrag
     * @param integer $requestid
     * @param string $logtype
     * @param string $params
     * @param integer $timestamp
     */
    public function log($requestid, $logtype, $params, $timestamp)
    {
        $this->_insert(
            'dragonx_logging_logs',
            array(
                'requestid' => $requestid,
                'logtype' => $logtype,
                'params' => $params,
                'timestamp' => $this->_formatTimestamp($timestamp)
            )
        );
    }

    /**
     * Speichert einen neuen Fehlereintrag
     * @param integer $requestid
     * @param string $errortype
     * @param string $params
     * @param integer $timestamp
     */
    public function error($requestid, $errortype, $params, $timestamp)
    {
        $this->_insert(
            'dragonx_logging_errors',
            array(
                'requestid' => $requestid,
                'errortype' => $errortype,
                'params' => $params,
                'timestamp' => $this->_formatTimestamp($timestamp)
            )
        );
    }
}
