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
	 * @param string $classname
	 * @param string $methodname
	 * @return boolean
	 */
	private function _authenticateRequired($classname, $methodname)
	{
        try {
            $reflectionClass = new Zend_Reflection_Class($classname);
            return $reflectionClass->getMethod($methodname)->getDocblock()->hasTag('dragonx_account_authenticate');
        } catch (Exception $exception) {
        }
        return false;
	}

    /**
     * Prüft bei jedem Request die Authentifizierung
     * @param Dragon_Json_Server_Request_Http $request
     */
    public function preDispatch(Dragon_Json_Server_Request_Http $request)
    {
        list ($classname, $methodname) = $request->parseMethod();
		if (!$this->_authenticateRequired($classname, $methodname)) {
		    return;
		}

		$params = $request->getRequiredParams(array('sessionhash'));
        $logicSession = new DragonX_Account_Logic_Session();
        $recordAccount = $logicSession->getAccount($params['sessionhash']);
        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->requestAccount($recordAccount);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_LoadAccount_Interface',
            array($recordAccount)
        );

        Zend_Registry::set('recordAccount', $recordAccount);
    }

    /**
     * Fügt bei jedem Service die Authentifizierungsanforderung hinzu
     * @param Zend_Json_Server_Smd $servicemap
     */
    public function servicemap(Zend_Json_Server_Smd $servicemap)
    {
        foreach ($servicemap->getServices() as $servicename => $service) {
            $servicearray = explode('.', $servicename);
            $methodname = array_pop($servicearray);
        	if (!$this->_authenticateRequired(implode('_', $servicearray), $methodname)) {
                continue;
            }
            $service->addParams(array(
                array(
                    'type' => 'string',
                    'name' => 'sessionhash',
                    'optional' => false,
                ),
            ));
        }
    }
}
