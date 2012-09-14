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
 * Serviceklasse zur Registrierung und Verwaltung von Accounts
 */
class DragonX_Account_Service_Account
{
    /**
     * Erstellt einen temporären Account der nur begrenzt gültig ist
     * @return array
     */
	public function temporaryAccount()
	{
        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionhash = $logicAccount->loginAccount(
            $logicAccount->temporaryAccount()
        );
        return array('sessionhash' => $sessionhash);
	}

	/**
     * Speichert den Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @dragonx_account_authenticate
     */
    public function saveAccount($identity, $credential)
    {
        $recordAccount = Zend_Registry::get('recordAccount');
        if (isset($recordAccount->identity)) {
            throw new Exception('account already saved');
        }
        $logicAccount = new DragonX_Account_Logic_Account();
        $configValidation = new Dragon_Application_Config('dragonx/account/validation');
        $logicAccount->saveAccount($recordAccount, $identity, $credential, $configValidation->validationhash);
    }

    /**
     * Registriert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     */
    public function registerAccount($identity, $credential)
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $configValidation = new Dragon_Application_Config('dragonx/account/validation');
        $accountid = $logicAccount->registerAccount($identity, $credential, $configValidation->validationhash);
    }

    /**
     * Validiert einen Account mit dem Hash der Validierungsabfrage
     * @param string $validationhash
     */
    public function validateAccount($validationhash)
    {
        $logicValidation = new DragonX_Account_Logic_Validation();
        $logicValidation->validate($validationhash);
    }

    /**
     * Gibt die AccountID der Identity und dem Credential zurück
     * @return array
     * @dragonx_account_authenticate
     */
    public function authenticateAccount()
    {
        return array('accountid' => Zend_Registry::get('recordAccount')->id);
    }

    /**
     * Meldet den übergebenen Account an
     * @param string $identity
     * @param string $credential
     * @return array
     */
    public function loginAccount($identity, $credential)
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $sessionhash = $logicAccount->loginAccount(
            $logicAccount->authenticateAccount($identity, $credential)
        );
        return array('sessionhash' => $sessionhash);
    }

    /**
     * Meldet den übergebenen Account ab
     * @param string $sessionhash
     */
    public function logoutAccount($sessionhash)
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->logoutAccount($sessionhash);
    }

    /**
     * Ändert die E-Mail Adresse trägt eine neue Validierungabfrage ein
     * @param string $newidentity
     * @dragonx_account_authenticate
     */
    public function changeIdentity($newidentity)
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $configValidation = new Dragon_Application_Config('dragonx/account/validation');
        $logicAccount->changeIdentity(Zend_Registry::get('recordAccount'), $newidentity, $configValidation->validationhash);
    }

    /**
     * Ändert das Passwort für den Account
     * @param string $newcredential
     * @dragonx_account_authenticate
     */
    public function changeCredential($newcredential)
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->changeCredential(Zend_Registry::get('recordAccount'), $newcredential);
    }

    /**
     * Setzt den Löschstatus des Accounts sodass dieser gelöscht werden kann
     * @dragonx_account_authenticate
     */
    public function deleteAccount()
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->deleteAccount(Zend_Registry::get('recordAccount'));
    }

    /**
     * Gibt des Löschstatus des Accounts zurück
     * @dragonx_account_authenticate
     * @return array
     */
    public function getDeletion()
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $recordDeletion = $logicAccount->getDeletion(Zend_Registry::get('recordAccount'));

        if (isset($recordDeletion)) {
	        return array(
	            'timestamp' => $recordDeletion->timestamp,
	        );
        }
    }

    /**
     * Setzt den Löschstatus des Accounts zurück
     * @dragonx_account_authenticate
     */
    public function deleteDeletion()
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $logicAccount->deleteDeletion(Zend_Registry::get('recordAccount'));
    }
}
