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
 * Controller zur Verarbeitung von Passwort vergessen Abfragen
 */
class CredentialController extends DragonX_Homepage_Controller_Abstract
{
    /**
     * Sorgt dafür dass der Controller nur mit Accountverwaltung erreichbar ist
     * @throw Zend_Controller_Dispatcher_Exception
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Account')
            || !Zend_Registry::get('Dragon_Package_Registry')->isAvailable('DragonX', 'Emailaddress')) {
            throw new Dragon_Application_Exception_User('incorrect controller', array('controllername' => $this->getRequest()->getControllerName()));
        }
    }

    /**
     * Sendet für die Identität eine Passwort vergessen E-Mail
     */
    public function requestAction()
    {
        try {
            $params = $this->getRequiredParams(array('emailaddress'));

	        $logicCredential = new DragonX_Emailaddress_Logic_Credential();
	        $configCredential = new Dragon_Application_Config('dragonx/emailaddress/credential');
	        $logicCredential->request($params['emailaddress'], $configCredential->credentiallink, $configCredential->hashmethod);
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse nicht vorhanden</div>');
            $this->_redirect('credential/showrequest');
        }

        $this->_helper->FlashMessenger('<div class="alert alert-success">E-Mail mit einem Link zum Zurücksetzen des Passworts versendet</div>');
        $this->_redirect('startpage/index');
    }

    /**
     * Zeigt das Formular zur Passwort vergessen Seite an
     */
    public function showrequestAction()
    {
        $this->render('request');
    }

    /**
     * Setzt das Passwort für die Passwort vergessen Anfrage zurück
     */
    public function resetAction()
    {
        try {
            $params = $this->getRequiredParams(array('credentialhash', 'newpassword'));

	        $logicCredential = new DragonX_Emailaddress_Logic_Credential();
	        $recordAccount = $logicCredential->reset($params['credentialhash'], $params['newpassword']);
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">Resetlink nicht korrekt</div>');
            $this->_redirect('credential/showrequest');
        }

        $logicSession = new DragonX_Account_Logic_Session();
        $sessionNamespace = new Zend_Session_Namespace();
        $sessionNamespace->sessionhash = $logicSession->loginAccount($recordAccount);

        $this->_helper->FlashMessenger('<div class="alert alert-success">Zurücksetzen des Passworts erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Zeigt das Formular zur Passwort zurücksetzen Seite an
     */
    public function showresetAction()
    {
        try {
    	    $params = $this->getRequiredParams(array('credentialhash'));
        } catch (Exception $exception) {
            $this->_redirect('credential/showrequest');
        }

    	$this->view->credentialhash = $params['credentialhash'];
        $this->render('reset');
    }
}
