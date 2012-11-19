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
     * Ändert die E-Mail Adresse trägt eine neue Validierungabfrage ein
     */
    public function changeemailaddressAction()
    {
        try {
            $params = $this->getRequiredParams(array('newemailaddress'));

	        $logicValidation = new DragonX_Emailaddress_Logic_Validation();
	        $configValidation = new Dragon_Application_Config('dragonx/emailaddress/validation');
	        $logicValidation->changeEmailaddress(Zend_Registry::get('recordAccount'), $params['newemailaddress'], $configValidation->validationlink);
        } catch (Exception $exception) {
            $this->_helper->FlashMessenger('<div class="alert alert-error">E-Mail Adresse nicht korrekt oder bereits vergeben</div>');
            $this->_redirect('account/showedit');
        }

        $this->_helper->FlashMessenger('<div class="alert alert-success">Änderung der E-Mail Adresse erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Ändert das Passwort für den Account
     */
    public function changepasswordAction()
    {
        $params = $this->getRequiredParams(array('newpassword'));

        $logicCredential = new DragonX_Emailaddress_Logic_Credential();
        $logicCredential->changePassword(Zend_Registry::get('recordAccount'), $params['newpassword']);

        $this->_helper->FlashMessenger('<div class="alert alert-success">Änderung des Passworts erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Setzt den Löschstatus des Accounts sodass dieser gelöscht werden kann
     */
    public function deleteaccountAction()
    {
        $logicDeletion = new DragonX_Account_Logic_Deletion();
        $logicDeletion->deleteAccount(Zend_Registry::get('recordAccount'));

        $this->_helper->FlashMessenger('<div class="alert alert-success">Löschung des Profils eingetragen</div>');
        $this->_redirect('administration');
    }

    /**
     * Setzt den Löschstatus des Accounts zurück
     */
    public function deletedeletionAction()
    {
        $logicDeletion = new DragonX_Account_Logic_Deletion();
        $logicDeletion->deleteDeletion(Zend_Registry::get('recordAccount'));

        $this->_helper->FlashMessenger('<div class="alert alert-success">Löschung des Profils zurückgesetzt</div>');
        $this->_redirect('administration');
    }

    /**
     * Zeigt das Formular zur Bearbeitung eines Accounts an
     */
    public function showeditAction()
    {
        $this->render('edit');
    }
}
