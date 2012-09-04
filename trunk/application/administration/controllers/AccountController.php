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
     * Ändert die E-Mail Adresse trägt eine neue Validierungabfrage ein
     */
    public function changeidentityAction()
    {
        try {
            $params = $this->getRequiredParams(array('newidentity'));

            $logicAccount = new DragonX_Account_Logic_Account();
            $sessionNamespace = new Zend_Session_Namespace();
            $configValidation = new Dragon_Application_Config('dragonx/account/validation');
            $logicAccount->changeIdentity($sessionNamespace->recordAccount, $params['newidentity'], $configValidation->validationlink);
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
        $params = $this->getRequiredParams(array('newcredential'));

        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionNamespace = new Zend_Session_Namespace();
        $logicAccount->changeCredential($sessionNamespace->recordAccount, $params['newcredential']);

        $this->_helper->FlashMessenger('<div class="alert alert-success">Änderung des Passworts erfolgreich</div>');
        $this->_redirect('administration');
    }

    /**
     * Setzt den Löschstatus des Accounts sodass dieser gelöscht werden kann
     */
    public function deleteaccountAction()
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionNamespace = new Zend_Session_Namespace();
        $logicAccount->deleteAccount($sessionNamespace->recordAccount);

        $this->_redirect('administration');
    }

    /**
     * Setzt den Löschstatus des Accounts zurück
     */
    public function deletedeletionAction()
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionNamespace = new Zend_Session_Namespace();
        $logicAccount->deleteDeletion($sessionNamespace->recordAccount);

        $this->_helper->FlashMessenger('<div class="alert alert-success">Löschung des Accounts zurückgesetzt</div>');
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
