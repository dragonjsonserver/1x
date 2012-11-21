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
 * Klasse zur Verarbeitung von JsonRPC Requests
 */
class Dragon_Json_Server extends Zend_Json_Server
{
	/**
	 * Loggt die übergebene Ausnahme wenn das Paket DragonX Log eingebunden ist
	 * @param Exception $exception
	 */
	private function _logException(Exception $exception)
	{
        if (!Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Log')) {
        	return;
        }
        $logicLog = new DragonX_Log_Logic_Log();
        $logicLog->automatic($exception);
	}

    /**
     * Verarbeitet den JsonRPC Request mit der eigenen Ausnahmeklasse
     * @param Zend_Server_Method_Definition $invocable
     * @param array $params
     * @return mixed
     */
    protected function _dispatch(Zend_Server_Method_Definition $invocable, array $params)
    {
    	try {
    		return parent::_dispatch($invocable, $params);
    	} catch (Exception $exception) {
    		$this->_logException($exception);
    		if ($exception instanceof Dragon_Application_Exception_Abstract) {
    			$this->fault($exception->getMessage(), $exception->getCode(), $exception->getData());
    		} else {
    			$this->fault($exception->getMessage(), $exception->getCode(), $exception);
    		}
    	}
    }

    /**
     * Verarbeitet den JsonRPC Request mit Pre-/Postdispatch Aufrufen
     */
    protected function _handle()
    {
        try {
            $pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
            $request = $this->getRequest();
            $pluginregistry->invoke(
                'Dragon_Json_Plugin_PreDispatch_Interface',
                array($request)
            );
            parent::_handle();
            $pluginregistry->invoke(
                'Dragon_Json_Plugin_PostDispatch_Interface',
                array($request, $this->getResponse())
            );
        } catch (Exception $exception) {
            $this->_logException($exception);
        	if ($exception instanceof Dragon_Application_Exception_Abstract) {
        		$this->fault($exception->getMessage(), $exception->getCode(), $exception->getData());
        	} else {
        		$this->fault($exception->getMessage(), $exception->getCode(), $exception);
        	}
        }
    }

    /**
     * Verarbeitet einen Aufruf des Json Servers
     * @param Dragon_Json_Server_Request_Http $request
     * @return mixed|null|Zend_Json_Server_Response
     * @throws InvalidArgumentException
     */
    public function handle($request = null)
    {
        $requestmethod = null;
        if (isset($request)) {
            if (!$request instanceof Dragon_Json_Server_Request_Http) {
                throw new Dragon_Application_Exception_System('incorrect requestclass', array('requestclass' => get_class($request)));
            }
            $requestmethod = 'POST';
        } else {
            $request = new Dragon_Json_Server_Request_Http();
        }
        if (!isset($requestmethod)) {
            $requestmethod = $_SERVER['REQUEST_METHOD'];
        }
        $configCache = new Dragon_Application_Config('dragon/json/cache');
        if (!isset($configCache->filepath) || !Zend_Server_Cache::get($configCache->filepath, $this)) {
            $packageregistry = Zend_Registry::get('Dragon_Package_Registry');
            foreach ($packageregistry->getClassnames('Service') as $servicename) {
                $this->setClass($servicename, str_replace('_', '.', $servicename));
            }
            if (isset($configCache->filepath)) {
                Zend_Server_Cache::save($configCache->filepath, $this);
            }
        }
        switch ($requestmethod) {
            case 'POST':
                $this->setResponse(new Dragon_Json_Server_Response_Http());
                $autoemitresponse = $this->autoEmitResponse();
                if ($autoemitresponse) {
                	$this->setAutoEmitResponse(false);
                }
                $response = parent::handle($request);
                $result = $response->getResult();
                if (isset($result['result'])) {
                	$subresult = &$result['result'];
                } else {
                	$subresult = &$result;
                }
                if (is_array($subresult)) {
                	foreach ($request->getMap() as $key => $value) {
                        if (isset($subresult[$key])) {
                        	$subresult[$value] = $subresult[$key];
                        }
                	}
                	$response->setResult($result);
                }
                if ($autoemitresponse) {
                    $this->setAutoEmitResponse(true);
		            echo $response;
		            return;
                }
                return $response;
                break;
            case 'GET':
                header('Content-Type: application/json');
                $servicemap = $this->getServiceMap();
                Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
                    'Dragon_Json_Plugin_Servicemap_Interface',
                    array($servicemap)
                );
                echo $servicemap;
                break;
        }
    }

    /**
     * Verarbeitet einen Multirequest des Json Servers
     * @param array $requests
     * @return null|array
     * @throws InvalidArgumentException
     */
    public function handleMultirequest(array $requests = null)
    {
    	if (!isset($requests)) {
			$json = file_get_contents('php://input');
			$requests = Zend_Json::decode($json);
    	}
    	if (count($requests) == 0) {
    		throw new Dragon_Application_Exception_User('missing requests');
    	}
    	$autoemitresponse = $this->autoEmitResponse();
		$this->setAutoEmitResponse(false);
		$params = array();
		$responses = array();
		foreach ($requests as $request) {
		    $request['params'] += $params;
		    $response = $this->handle(new Dragon_Json_Server_Request_Http($request));
		    if ($autoemitresponse) {
		    	$response->sendHeaders();
		    }
		    $response = $response->toArray();
		    if (isset($response['result']) && is_array($response['result'])) {
		        $params += $response['result'];
		    }
		    $responses[] = $response;
		}
		if (!$autoemitresponse) {
			return $responses;
		}
		echo Zend_Json::encode($responses);
    }
}
