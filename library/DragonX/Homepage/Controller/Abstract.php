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
 * Controller zum Setzen aller Daten des Layouts
 */
abstract class DragonX_Homepage_Controller_Abstract extends Zend_Controller_Action
{
    /**
     * Setzt alle Daten des Layouts aus den Einstellungsdateien
     * @throw Zend_Controller_Dispatcher_Exception
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $recordAccount = null;
        if (Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Account')) {
            $sessionNamespace = new Zend_Session_Namespace();
            $this->view->recordAccount = $recordAccount = $sessionNamespace->recordAccount;
        }

        $this->view->configApplication = new Dragon_Application_Config('dragon/application/application');

        $modulename = $this->getRequest()->getModuleName();
        $this->view->modulename = $modulename;
        $controllername = $this->getRequest()->getControllerName();
        $this->view->controllername = $controllername;
        $actionname = $this->getRequest()->getActionName();

        switch ($modulename) {
        	case 'homepage':
        		$this->view->configNavigation = new Dragon_Application_Config('dragonx/homepage/navigation');
        		break;
        	case 'administration':
		        if (!Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Account')) {
                    throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $controllername . ')');
		        }

		        $sessionNamespace = new Zend_Session_Namespace();
                if (!isset($sessionNamespace->recordAccount)) {
                	$frontController = $this->getFrontController();
                    $defaultcontrollername = $frontController->getDefaultControllerName();
                	$defaultactionname = $frontController->getDefaultAction();
                    $params = array();
                	if ($controllername != $defaultcontrollername
                	    ||
                	    $actionname != $defaultactionname) {
                	    if ($controllername != $defaultcontrollername) {
                	    	if ($actionname == $defaultactionname) {
	                	    	$params = array('redirect' => 'administration/' . $controllername);
                	    	} else {
	                	    	$params = array('redirect' => 'administration/' . $controllername . '/' . $actionname);
                	    	}
                	    } elseif ($actionname != $defaultactionname) {
                	    	$params = array('redirect' => 'administration/' . $controllername . '/' . $actionname);
                	    }
                	}
                	$redirect = '';
                	if (count($params) > 0) {
                		$redirect = '?' . http_build_query($params);
                	}
                	$this->_redirect('account/showlogin' . $redirect);
                }

                $logicAccount = new DragonX_Account_Logic_Account();
                $recordDeletion = $logicAccount->getDeletion($sessionNamespace->recordAccount);
                if (isset($recordDeletion)) {
	                $this->view->recordDeletion = $recordDeletion;
                	$this->view->configDeletion = new Dragon_Application_Config('dragonx/account/deletion');
                }

		        if (Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Acl')) {
                    $logicAcl = new DragonX_Acl_Logic_Acl();
	            	$this->view->resources = $logicAcl->getResources($recordAccount);
	            }

                $this->view->configNavigation = new Dragon_Application_Config('dragonx/homepage/navigation');
        		break;
        }
    }

    /**
     * Setzt alle Daten des Layouts der Session
     */
    public function postDispatch()
    {
        parent::postDispatch();

        $this->view->messages = $this->_helper->FlashMessenger->getMessages();
    }

    /**
     * Leitet mit einer Meldung auf die Startseite um wenn die Ressource fehlt
     * @param string $resource
     */
    public function guaranteeResource($resource)
    {
        if (!isset($this->view->resources) || !in_array($resource, $this->view->resources)) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">Berechtigung für Ressource "' . $resource . '" nicht vorhanden</div>');
            $this->_redirect('administration');
        }
    }

    /**
     * Gibt alle Parameter des Requests zurück
     * @return array
     */
    public function getParams($name)
    {
        return $this->_getAllParams();
    }

    /**
     * Prüft den erforderlichen Parameter und gibt dessen Wert zurück
     * @param string $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getRequiredParam($name)
    {
        if (!$this->_hasParam($name)) {
            throw new InvalidArgumentException('required param "' . $name . '"');
        }
        return $this->_getParam($name);
    }

    /**
     * Prüft die erforderlichen Parameter und gibt deren Werte zurück
     * @param array $names
     * @return array
     * @throws InvalidArgumentException
     */
    public function getRequiredParams($names)
    {
        $params = array();
        foreach ($names as $name) {
            $params[$name] = $this->getRequiredParam($name);
        }
        return $params;
    }

    /**
     * Gibt den optionalen Parameter oder den Standardwert zurück
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOptionalParam($name, $default = null)
    {
        return $this->_getParam($name, $default);
    }

    /**
     * Gibt die optionalen Parameter oder die Standardwerte zurück
     * @param array $names
     * @return array
     */
    public function getOptionalParams(array $names)
    {
        $params = array();
        foreach ($names as $name => $default) {
            $params[$name] = $this->getOptionalParam($name, $default);
        }
        return $params;
    }
}
