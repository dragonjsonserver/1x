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
class DragonX_Log_Logic_Request
{
    /**
     * @var DragonX_Log_Record_Request
     */
    private static $_recordRequest;

    /**
     * Speichert die Daten des Requests
     * @param string $rpcmethod
     * @param string $rpcid
     * @param string $rpcversion
     * @param array $requestparams
     */
    public function request($rpcmethod, $rpcid, $rpcversion, array $requestparams)
    {
        $configBlacklist = new Dragon_Application_Config('dragonx/log/blacklist');
        $configBlacklistArray = $configBlacklist->toArray();
        foreach ($requestparams as $name => &$param) {
            if (in_array($name, $configBlacklistArray)) {
                $param = '';
            }
        }
        unset($param);

        self::$_recordRequest = new DragonX_Log_Record_Request(array(
            'rpcmethod' => $rpcmethod,
            'rpcid' => $rpcid,
            'rpcversion' => $rpcversion,
            'requestparams' => Zend_Json::encode($requestparams),
            'requesttimestamp' => time(),
        ));
        Zend_Registry::get('DragonX_Storage_Engine')->save(self::$_recordRequest);

        if (Zend_Registry::isRegistered('Zend_Log')) {
            $logger = Zend_Registry::get('Zend_Log');
            $logger->setEventItem('requestid', self::$_recordRequest->id);
        }
    }

    /**
     * Speichert die Daten des Response
     * @param array $response
     */
    public function response(array $response)
    {
        if (!isset(self::$_recordRequest)) {
            return;
        }

        $configBlacklist = new Dragon_Application_Config('dragonx/log/blacklist');
        $configBlacklistArray = $configBlacklist->toArray();
        if (isset($response['result']) && is_array($response['result'])) {
            foreach ($response['result'] as $name => &$param) {
                if (in_array($name, $configBlacklistArray)) {
                    $param = '';
                }
                unset($param);
            }
        }

        self::$_recordRequest->fromArray(array(
            'response' => Zend_Json::encode($response),
            'responsetimestamp' => time(),
        ));
        Zend_Registry::get('DragonX_Storage_Engine')->save(self::$_recordRequest);
    }
}
