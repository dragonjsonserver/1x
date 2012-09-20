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
 * Plugin um Responses zusätzliche Informationen mitzuschicken
 */
class DragonX_Clientmessage_Plugin_Clientmessage implements Dragon_Json_Plugin_PostDispatch_Interface
{
	/**
	 * Vergleich zwei Clientnachrichten zur Sortierung nach dem Timestamp
	 * @param array $messageA
	 * @param array $messageB
	 */
	private function _compareMessages($messageA, $messageB)
	{
        if ($messageA['timestamp'] == $messageB['timestamp']) {
            return 0;
        }
        if ($messageA['timestamp'] > $messageB['timestamp']) {
            return 1;
        }
        return -1;
	}

    /**
     * Hängt zusätzliche Informationen an jeden Response an
     * @param Dragon_Json_Server_Request_Http $request
     * @param Dragon_Json_Server_Response_Http $response
     */
    public function postDispatch(Dragon_Json_Server_Request_Http $request, Dragon_Json_Server_Response_Http $response)
    {
    	$lastResponse = $request->getOptionalParam('timestamp');
        if (isset($lastResponse) && $lastResponse == -1) {
        	return;
        }
        $actualResponse = time();
        $messages = array();
        if (isset($lastResponse)) {
        	$pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
        	$plugins = $pluginregistry->getPlugins('DragonX_Clientmessage_Plugin_Source_Interface');
        	foreach ($plugins as $plugin) {
                $list = $plugin->getClientmessages($lastResponse, $actualResponse);
                foreach ($list as $record) {
                	if (!isset($messages[$record->key])) {
                		$messages[$record->key] = array();
                	}
                	$messages[$record->key][] = array('timestamp' => $record->timestamp, 'result' => Zend_Json::decode($record->result));
                }
        	}
        	foreach ($messages as $key => &$submessages) {
        		usort($submessages, array($this, '_compareMessages'));
        	}
        	unset($submessages);
        }
    	$response->setResult(array(
    	    'result' => $response->getResult(),
    	    'timestamp' => $actualResponse,
        ) + $messages);
    }
}
