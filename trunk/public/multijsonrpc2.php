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

require 'bootstrap.php';
$json = file_get_contents('php://input');
$requests = Zend_Json::decode($json);
$jsonserver = new Dragon_Json_Server();
$jsonserver->setAutoEmitResponse(false);
$params = array();
$responses = array();
foreach ($requests as $request) {
    $request['params'] += $params;
    $response = $jsonserver->handle(new Dragon_Json_Server_Request_Http($request));
    if ($response->isError()) {
        $responses[] = array(
            'error' => $response->getError()->toArray(),
            'id' => $response->getId(),
        );
    } else {
        $result = $response->getResult();
        $responses[] = array(
            'result' => $result,
            'id' => $response->getId(),
        );
        if (is_array($result)) {
            $params += $result;
        }
    }
}
echo Zend_Json::encode($responses);
