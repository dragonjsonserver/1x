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
     * Sendet für die Identität eine Passwort vergessen E-Mail
     */
    public function requestAction()
    {
        try {
            $params = $this->getRequiredParams(array('identity'));

	        $logicCredential = new DragonX_Account_Logic_Credential();
	        $configCredential = new Dragon_Application_Config('dragonx/account/credential');
	        $logicCredential->request($params['identity'], $configCredential->credentiallink);
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('E-Mail Adresse nicht vorhanden');
            $this->_redirect('credential/showrequest');
        }

        $this->_helper->FlashMessenger('E-Mail mit einem Link zum Zurücksetzen des Passworts versendet');
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
            $params = $this->getRequiredParams(array('credentialhash', 'newcredential'));

	        $logicCredential = new DragonX_Account_Logic_Credential();
	        $recordAccount = $logicCredential->reset($params['credentialhash'], $params['newcredential']);
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('Resetlink nicht korrekt');
            $this->_redirect('credential/showrequest');
        }

        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->loginAccount($recordAccount->identity, $params['newcredential']);

        $this->_helper->FlashMessenger('Zurücksetzen des Passworts erfolgreich');
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
