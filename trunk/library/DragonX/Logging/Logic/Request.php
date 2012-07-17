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
 * Logik zur Speicherung der Request- und Responsedaten
 */
class DragonX_Logging_Logic_Request extends DragonX_Database_Logic_Abstract
{
    /**
     * @var integer
     */
    private static $requestid;

    /**
     * @var array
     */
    private $_blacklist = array();

    /**
     * Lädt die Einstellungen der Blacklist für die Parameter
     */
    public function __construct()
    {
        $blacklist = new Dragon_Application_Config('dragonx/logging/blacklist');
        $this->_blacklist = $blacklist->toArray();
    }

    /**
     * Gibt die aktuelle RequestID zurück
     * @return integer
     */
    public static function getRequestID()
    {
        return self::$requestid;
    }

    /**
     * Speichert die Daten des Requests
     * @param string $method
     * @param string $id
     * @param string $version
     * @param array $requestparams
     */
    public function request($method, $id, $version, array $requestparams)
    {
        foreach ($requestparams as $paramName => &$requestparam) {
            if (in_array($paramName, $this->_blacklist)) {
                $requestparam = '';
            }
        }
        unset($requestparam);
        $modelRequest = new DragonX_Logging_Model_Request();
        self::$requestid = $modelRequest->request($method, $id, $version, Zend_Json::encode($requestparams), time());
    }

    /**
     * Speichert die Daten des Response
     * @param array $response
     */
    public function response(array $response)
    {
        if (isset($response['result']) && is_array($response['result'])) {
            foreach ($response['result'] as $name => &$param) {
                if (in_array($name, $this->_blacklist)) {
                    $responseparam = '';
                }
                unset($responseparam);
            }
        }
        $modelRequest = new DragonX_Logging_Model_Request();
        $modelRequest->response(self::$requestid, Zend_Json::encode($response), time());
    }
}
