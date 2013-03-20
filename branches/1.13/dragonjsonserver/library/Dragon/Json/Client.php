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
 * Klasse zum Versenden von JsonRPC Requests
 */
class Dragon_Json_Client
{
    /**
     * @var Zend_Http_Client
     */
    private $_httpclient;

    /**
     * Nimmt den HTTP Client mit der Verbindung zum JsonRPC Server entgegen
     * @param string $uri
     */
    public function __construct(Zend_Http_Client $httpclient)
    {
        $this->_httpclient = $httpclient;
    }

    /**
     * Sendet den übergebenen Request zum Server und gibt das Result zurück
     * @param Dragon_Json_Server_Request_Http $request
     * @return array
     */
    public function send(Dragon_Json_Server_Request_Http $request)
    {
        $request->addParam(-1, 'timestamp');
        $body = $this->_httpclient
            ->setRawData($request->toJson())
            ->request('POST')
            ->getBody();
        try {
	        $response = Zend_Json::decode($body);
        } catch (Exception $exception) {
            throw new Dragon_Application_Exception_System('decoding failed', array('message' => $exception->getMessage(), 'body' => $body));
        }
        if (!is_array($response) || !array_key_exists('id', $response)) {
            throw new Dragon_Application_Exception_System('invalid response', array('response' => $response, 'body' => $body));
        }
        if (array_key_exists('error', $response)) {
            throw new Dragon_Application_Exception_System($response['error']['message'], array('error' => $response['error']));
        }
        if (!array_key_exists('result', $response)) {
            throw new Dragon_Application_Exception_System('invalid response', array('response' => $response, 'body' => $body));
        }
        return $response['result'];
    }
}
