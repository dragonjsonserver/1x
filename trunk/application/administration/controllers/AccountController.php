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
class Administration_AccountController extends DragonX_Homepage_Controller_Abstract
{
    /**
     * Meldet den derzeit eingeloggten Account ab
     */
    public function logoutAction()
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->logoutAccount();

        $this->_helper->FlashMessenger('<div class="alert alert-success">Abmeldung erfolgreich</div>');
        $this->_redirect('');
    }

    /**
     * Speichert den Account mit der Identity und dem Credential
     */
    public function saveAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $recordAccount = $sessionNamespace->recordAccount;
        if (isset($recordAccount->identity)) {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
        }

        try {
            $params = $this->getRequiredParams(array('identity', 'credential'));

            $logicAccount = new DragonX_Account_Logic_Account();
            $configValidation = new Dragon_Application_Config('dragonx/account/validation');
            $logicAccount->saveAccount($recordAccount, $params['identity'], $params['credential'], $configValidation->validationlink);
        } catch (InvalidArgumentException $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse nicht korrekt</div>');
            $this->_redirect('account/showedit');
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse bereits vergeben</div>');
            $this->_redirect('account/showedit');
        }

        $this->_helper->FlashMessenger('<div class="alert alert-success">Speicherung des Profils erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Zeigt das Formular zur Speicherung eines Accounts an
     */
    public function showsaveAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $recordAccount = $sessionNamespace->recordAccount;
        if (isset($recordAccount->identity)) {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
        }

        $this->render('save');
    }

    /**
     * Ändert die E-Mail Adresse trägt eine neue Validierungabfrage ein
     */
    public function changeidentityAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $recordAccount = $sessionNamespace->recordAccount;
    	if (!isset($recordAccount->identity)) {
    		throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
    	}

        try {
            $params = $this->getRequiredParams(array('newidentity'));

            $logicAccount = new DragonX_Account_Logic_Account();
            $configValidation = new Dragon_Application_Config('dragonx/account/validation');
            $logicAccount->changeIdentity($recordAccount, $params['newidentity'], $configValidation->validationlink);
        } catch (InvalidArgumentException $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse nicht korrekt</div>');
            $this->_redirect('account/showedit');
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse bereits vergeben</div>');
            $this->_redirect('account/showedit');
        }

        $this->_helper->FlashMessenger('<div class="alert alert-success">Änderung der E-Mail Adresse erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Ändert das Passwort für den Account
     */
    public function changecredentialAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $recordAccount = $sessionNamespace->recordAccount;
        if (!isset($recordAccount->identity)) {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
        }

        $params = $this->getRequiredParams(array('newcredential'));

        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionNamespace = new Zend_Session_Namespace();
        $logicAccount->changeCredential($recordAccount, $params['newcredential']);

        $this->_helper->FlashMessenger('<div class="alert alert-success">Änderung des Passworts erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Setzt den Löschstatus des Accounts sodass dieser gelöscht werden kann
     */
    public function deleteaccountAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $recordAccount = $sessionNamespace->recordAccount;
        if (!isset($recordAccount->identity)) {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
        }

        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionNamespace = new Zend_Session_Namespace();
        $logicAccount->deleteAccount($recordAccount);

        $this->_helper->FlashMessenger('<div class="alert alert-success">Löschung des Profils eingetragen</div>');
        $this->_redirect('administration');
    }

    /**
     * Setzt den Löschstatus des Accounts zurück
     */
    public function deletedeletionAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $recordAccount = $sessionNamespace->recordAccount;
        if (!isset($recordAccount->identity)) {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
        }

        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionNamespace = new Zend_Session_Namespace();
        $logicAccount->deleteDeletion($recordAccount);

        $this->_helper->FlashMessenger('<div class="alert alert-success">Löschung des Profils zurückgesetzt</div>');
        $this->_redirect('administration');
    }

    /**
     * Zeigt das Formular zur Bearbeitung eines Accounts an
     */
    public function showeditAction()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $recordAccount = $sessionNamespace->recordAccount;
        if (!isset($recordAccount->identity)) {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
        }

        $this->render('edit');
    }
}
