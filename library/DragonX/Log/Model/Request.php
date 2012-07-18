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
 * Model zur Speicherung der Request- und Responsedaten
 */
class DragonX_Log_Model_Request extends DragonX_Database_Model_Abstract
{
    /**
     * Speichert die Daten des Requests
     * @param string $method
     * @param string $id
     * @param string $version
     * @param string $requestparams
     * @param integer $requesttimestamp
     * @return integer
     */
    public function request($method, $id, $version, $requestparams, $requesttimestamp)
    {
        return $this->_insert(
            'dragonx_log_requests',
            array(
                'method' => $method,
                'id' => $id,
                'version' => $version,
                'requestparams' => $requestparams,
                'requesttimestamp' => $this->_formatTimestamp($requesttimestamp),
            )
        );
    }

    /**
     * Speichert die Daten des Response
     * @param $requestid
     * @param $response
     * @param $responsetimestamp
     */
    public function response($requestid, $response, $responsetimestamp)
    {
        $this->_update(
            'dragonx_log_requests',
            array(
                'response' => $response,
                'responsetimestamp' => $this->_formatTimestamp($responsetimestamp),
            ),
            array(
                'requestid' => $requestid,
            )
        );
    }
}
