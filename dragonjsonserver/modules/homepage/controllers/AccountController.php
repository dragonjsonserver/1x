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
 * Controller zur Registrierung und Verwaltung von Accounts
 */
class AccountController extends DragonX_Homepage_Controller_Abstract
{
	/**
	 * Sorgt dafür dass der Controller nur mit Accountverwaltung erreichbar ist
     * @throw Zend_Controller_Dispatcher_Exception
	 */
	public function preDispatch()
	{
		parent::preDispatch();

		if (!Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Account')) {
			throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
		}
	}

    /**
     * Registriert einen Account mit der E-Mail Adresse und dem Passwort
     */
    public function registerAction()
    {
        try {
            $params = $this->getRequiredParams(array('emailaddress', 'password'));

            $logicAccount = new DragonX_Account_Logic_Account();
            $recordAccount = $logicAccount->createAccount();
            $logicEmailaddress = new DragonX_Emailaddress_Logic_Emailaddress();
            $configValidation = new Dragon_Application_Config('dragonx/emailaddress/validation');
            $logicEmailaddress->linkAccount($recordAccount, $params['emailaddress'], $params['password'], $configValidation->validationlink);

            $logicSession = new DragonX_Account_Logic_Session();
            $sessionNamespace = new Zend_Session_Namespace();
            $sessionNamespace->sessionhash = $logicSession->loginAccount($recordAccount);
        } catch (InvalidArgumentException $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse nicht korrekt</div>');
            $this->_redirect('account/showregister');
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse bereits vergeben</div>');
            $this->_redirect('account/showregister');
        }

        $this->_helper->FlashMessenger('<div class="alert alert-success">Registrierung erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Zeigt das Formular zur Registrierung eines Accounts an
     */
    public function showregisterAction()
    {
        $this->render('register');
    }

    /**
     * Validiert einen Account mit dem Hash der Validierungsabfrage
     */
    public function validateAction()
    {
        try {
            $params = $this->getRequiredParams(array('validationhash'));

            $logicValidation = new DragonX_Emailaddress_Logic_Validation();
            $recordAccount = $logicValidation->validate($params['validationhash']);

            $logicSession = new DragonX_Account_Logic_Session();
            $sessionNamespace = new Zend_Session_Namespace();
            $sessionNamespace->sessionhash = $logicSession->loginAccount($recordAccount);
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">Validierungslink nicht korrekt</div>');
            $this->_redirect('account/showlogin');
        }

        $this->_helper->FlashMessenger('<div class="alert alert-success">Validierung des Profils erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Meldet den übergebenen Account an
     */
    public function loginAction()
    {
    	$redirect = $this->getOptionalParam('redirect', 'administration');
    	try {
            $params = $this->getRequiredParams(array('emailaddress', 'password'));

            $logicEmailaddress = new DragonX_Emailaddress_Logic_Emailaddress();
            $recordAccount = $logicEmailaddress->getAccount(
                $params['emailaddress'], $params['password']
            );

            $logicSession = new DragonX_Account_Logic_Session();
            $sessionNamespace = new Zend_Session_Namespace();
            $sessionNamespace->sessionhash = $logicSession->loginAccount($recordAccount);
    	} catch (Exception $exception) {
	        $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse oder Passwort nicht korrekt</div>');
	        if ($redirect == 'administration') {
	        	$redirect = '';
	        } else {
	        	$redirect = '?' . http_build_query(array('redirect' => $redirect));
	        }
	        $this->_redirect('account/showlogin' . $redirect);
    	}

        $this->_helper->FlashMessenger('<div class="alert alert-success">Anmeldung erfolgreich</div>');
        $this->_redirect($redirect);
    }

    /**
     * Zeigt das Formular zur Anmeldung eines Accounts an
     */
    public function showloginAction()
    {
    	$this->view->redirect = $this->getOptionalParam('redirect');
        $this->render('login');
    }

    /**
     * Meldet den derzeit eingeloggten Account ab
     */
    public function logoutAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        if (!isset($sessionNamespace->sessionhash)) {
            $this->_redirect('');
        }
        $logicSession = new DragonX_Account_Logic_Session();
        $logicSession->logoutAccount($sessionNamespace->sessionhash);
        $sessionNamespace->unsetAll();

        $this->_helper->FlashMessenger('<div class="alert alert-success">Abmeldung erfolgreich</div>');
        $this->_redirect('');
    }
}
