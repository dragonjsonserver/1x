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
     * Registriert einen Account mit der Identity und dem Credential
     */
    public function registerAction()
    {
        try {
            $params = $this->getRequiredParams(array('identity', 'credential'));

            $logicAccount = new DragonX_Account_Logic_Account();
            $configValidation = new Dragon_Application_Config('dragonx/account/validation');
            $logicAccount->registerAccount($params['identity'], $params['credential'], $configValidation->validationlink);
            $logicAccount->loginAccount($params['identity'], $params['credential']);
        } catch (InvalidArgumentException $exception) {
            $this->_helper->FlashMessenger('E-Mail Adresse nicht korrekt');
            $this->_redirect('account/showregister');
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('E-Mail Adresse bereits vergeben');
            $this->_redirect('account/showregister');
        }

        $this->_helper->FlashMessenger('Registrierung erfolgreich');
        $this->_redirect('startpage/index');
    }

    /**
     * Validiert einen Account mit dem Hash der Validierungsabfrage
     */
    public function validateAction()
    {
        try {
            $params = $this->getRequiredParams(array('validationhash'));

            $logicValidation = new DragonX_Account_Logic_Validation();
            $recordAccount = $logicValidation->validate($params['validationhash']);
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('Validierungslink nicht korrekt');
            $this->_redirect('account/showlogin');
        }

        $sessionNamespace = new Zend_Session_Namespace();
        $sessionNamespace->recordAccount = $recordAccount;

        $this->_helper->FlashMessenger('Validierung des Profils erfolgreich');
        $this->_redirect('startpage/index');
    }

    /**
     * Zeigt das Formular zur Registrierung eines Accounts an
     */
    public function showregisterAction()
    {
        $this->render('register');
    }

    /**
     * Meldet den übergebenen Account an
     */
    public function loginAction()
    {
    	try {
            $params = $this->getRequiredParams(array('identity', 'credential'));

	        $logicAccount = new DragonX_Account_Logic_Account();
	        $logicAccount->loginAccount($params['identity'], $params['credential']);
    	} catch (Exception $exception) {
	        $this->_helper->FlashMessenger('E-Mail Adresse oder Passwort nicht korrekt');
	        $this->_redirect('account/showlogin');
    	}

        $this->_helper->FlashMessenger('Anmeldung erfolgreich');
        $this->_redirect('startpage/index');
    }

    /**
     * Zeigt das Formular zur Anmeldung eines Accounts an
     */
    public function showloginAction()
    {
        $this->render('login');
    }

    /**
     * Meldet den übergebenen Account ab
     */
    public function logoutAction()
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->logoutAccount();

        $this->_helper->FlashMessenger('Abmeldung erfolgreich');
        $this->_redirect('startpage/index');
    }
}
