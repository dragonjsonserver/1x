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
 * Plugin zur Abfrage der Authentifizierung bei jedem Request
 */
class DragonX_Account_Plugin_Account
    implements Dragon_Json_Plugin_PreDispatch_Interface,
               Dragon_Json_Plugin_Servicemap_Interface
{
	/**
	 * Prüft ob der Service einen Account benötigt
	 * @param string $servicename
	 * @return boolean
	 */
	private function _authenticateRequired($servicename)
	{
        $servicearray = explode('.', $servicename);
        $methodname = array_pop($servicearray);
        $reflectionClass = new Zend_Reflection_Class(implode('_', $servicearray));
        return $reflectionClass->getMethod($methodname)->getDocblock()->hasTag('dragonx_account_authenticate');
	}

    /**
     * Prüft bei jedem Request ist die Authentifizierung
     * @param Dragon_Json_Server_Request_Http $request
     */
    public function preDispatch(Dragon_Json_Server_Request_Http $request)
    {
		if (!$this->_authenticateRequired($request->getMethod())) {
		    return;
		}

		$params = $request->getRequiredParams(array('identity', 'credential'));
        $logicAccount = new DragonX_Account_Logic_Account();
        $recordAccount = $logicAccount->authenticateAccount($params['identity'], $params['credential']);
        Zend_Registry::set('recordAccount', $recordAccount);

        if (Zend_Registry::isRegistered('Zend_Log')) {
            $logger = Zend_Registry::get('Zend_Log');
            $logger->setEventItem('accountid', $recordAccount->id);
        }
    }

    /**
     * Fügt bei jedem Service die Authentifizierungsanforderung hinzu
     * @param Zend_Json_Server_Smd $servicemap
     */
    public function servicemap(Zend_Json_Server_Smd $servicemap)
    {
        foreach ($servicemap->getServices() as $servicename => $service) {
        	if (!$this->_authenticateRequired($servicename)) {
                continue;
            }
            $service->addParams(array(
                array(
                    'type' => 'string',
                    'name' => 'identity',
                    'optional' => false,
                ),
                array(
                    'type' => 'string',
                    'name' => 'credential',
                    'optional' => false,
                ),
            ));
        }
    }
}
