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
 * Logikklasse zur Registrierung und Authentifizierung von Accounts
 */
class DragonX_Account_Logic_Account
{
    /**
     * Erstellt einen temporären Account der nur begrenzt gültig ist
     * @return DragonX_Account_Record_Account
     */
    public function temporaryAccount()
    {
        $recordAccount = new DragonX_Account_Record_Account();
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordAccount);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_TemporaryAccount_Interface',
            array($recordAccount)
        );

        return $recordAccount;
    }

    /**
     * Speichert einen Account mit der Identity und dem Credential
     * @param DragonX_Account_Record_Account $recordAccount
     * @param string $identity
     * @param string $credential
     * @param Zend_Config $configMail
     * @throws InvalidArgumentException
     */
    public function saveAccount(DragonX_Account_Record_Account $recordAccount, $identity, $credential, Zend_Config $configMail)
    {
        $recordAccount->fromArray(array(
            'credential' => md5($credential),
        ));
        $recordAccount->validateIdentity($identity);
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordAccount);

        $logicValidation = new DragonX_Account_Logic_Validation();
        $logicValidation->request($recordAccount, $configMail);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_SaveAccount_Interface',
            array($recordAccount)
        );
    }

    /**
     * Registriert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @param Zend_Config $configMail
     * @return integer
     * @throws InvalidArgumentException
     */
    public function registerAccount($identity, $credential, Zend_Config $configMail)
    {
        $recordAccount = new DragonX_Account_Record_Account(array(
            'credential' => md5($credential),
        ));
        $recordAccount->validateIdentity($identity);
    	Zend_Registry::get('DragonX_Storage_Engine')->save($recordAccount);

    	$logicValidation = new DragonX_Account_Logic_Validation();
    	$logicValidation->request($recordAccount, $configMail);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_RegisterAccount_Interface',
            array($recordAccount)
        );

    	return $recordAccount->id;
    }

    /**
     * Authentifiziert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @return integer
     * @throws InvalidArgumentException
     */
    public function authenticateAccount($identity, $credential)
    {
        $identity = strtolower($identity);
    	$listAccounts = Zend_Registry::get('DragonX_Storage_Engine')->loadByConditions(
    	    new DragonX_Account_Record_Account(),
    	    array('identity' => $identity, 'credential' => md5($credential))
    	);
        if (count($listAccounts) == 0) {
            throw new InvalidArgumentException('incorrect authenticate');
        }
        return $listAccounts[0];
    }

    /**
     * Meldet einen Account mit der Identity und dem Credential an
     * @param DragonX_Account_Record_Account $recordAccount
     * @return string
     * @throws InvalidArgumentException
     */
    public function loginAccount(DragonX_Account_Record_Account $recordAccount)
    {
        $configSession = new Dragon_Application_Config('dragonx/account/session');
        $timestamp = null;
        if (isset($configSession->lifetime)) {
        	$timestamp = time() + $configSession->lifetime;
        }
    	$recordSession = new DragonX_Account_Record_Session(array(
            'accountid' => $recordAccount->id,
            'sessionhash' => md5($recordAccount->id . '.' . time()),
            'timestamp' => $timestamp,
        ));
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordSession);
        return $recordSession->sessionhash;
    }

    /**
     * Gibt den Account zurück der zum übergebenen Sessionhash hinterlegt ist
     * @param string $sessionhash
     * @return DragonX_Account_Record_Account
     * @throws InvalidArgumentException
     */
    public function getAccount($sessionhash)
    {
        $listAccounts = Zend_Registry::get('DragonX_Storage_Engine')->loadBySqlStatement(
            new DragonX_Account_Record_Account(),
              "SELECT `dragonx_account_record_account`.`id`, `dragonx_account_record_account`.`identity` FROM `dragonx_account_record_account` "
            . "INNER JOIN `dragonx_account_record_session` ON `dragonx_account_record_session`.`accountid` = `dragonx_account_record_account`.`id` "
            . "WHERE `dragonx_account_record_session`.`sessionhash` = :sessionhash",
            array('sessionhash' => $sessionhash)
        );
        if (count($listAccounts) == 0) {
            throw new InvalidArgumentException('incorrect sessionhash');
        }
        return $listAccounts[0];
    }

    /**
     * Meldet den aktuell eingeloggten Account wieder ab
     * @param string $sessionhash
     */
    public function logoutAccount($sessionhash)
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');
    	$recordAccount = $this->getAccount($sessionhash);
    	$storage->deleteByConditions(
            new DragonX_Account_Record_Session(),
            array('sessionhash' => $sessionhash)
        );
        if (!isset($recordAccount->identity)) {
            Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
                'DragonX_Account_Plugin_DeleteAccount_Interface',
                array($recordAccount)
            );
            $storage->delete($recordAccount);
        }
    }

    /**
     * Ändert die E-Mail Adresse trägt eine neue Validierungabfrage ein
     * @param DragonX_Account_Record_Account $recordAccount
     * @param string $newidentity
     * @param Zend_Config $configMail
     * @throws InvalidArgumentException
     */
    public function changeIdentity(DragonX_Account_Record_Account $recordAccount, $newidentity, Zend_Config $configMail)
    {
    	$recordAccount->validateIdentity($newidentity);
    	Zend_Registry::get('DragonX_Storage_Engine')->save($recordAccount);

        $logicValidation = new DragonX_Account_Logic_Validation();
        $logicValidation->request($recordAccount, $configMail);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_ChangeIdentity_Interface',
            array($recordAccount)
        );
    }

    /**
     * Ändert das Passwort für den Account
     * @param DragonX_Account_Record_Account $recordAccount
     * @param string $newcredential
     */
    public function changeCredential(DragonX_Account_Record_Account $recordAccount, $newcredential)
    {
        $recordAccount->credential = md5($newcredential);
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordAccount);
    }

    /**
     * Setzt den Löschstatus des Accounts sodass dieser gelöscht werden kann
     * @param DragonX_Account_Record_Account $recordAccount
     */
    public function deleteAccount(DragonX_Account_Record_Account $recordAccount)
    {
    	$configDeletion = new Dragon_Application_Config('dragonx/account/deletion');
        Zend_Registry::get('DragonX_Storage_Engine')->save(
            new DragonX_Account_Record_Deletion(array(
                'accountid' => $recordAccount->id,
                'timestamp' => time() + $configDeletion->lifetime,
            ))
        );
    }

    /**
     * Gibt des Löschstatus des Accounts zurück
     * @param DragonX_Account_Record_Account $recordAccount
     * @return DragonX_Account_Record_Deletion|null
     */
    public function getDeletion(DragonX_Account_Record_Account $recordAccount)
    {
        $listDeletions = Zend_Registry::get('DragonX_Storage_Engine')->loadByConditions(
            new DragonX_Account_Record_Deletion(),
            array('accountid' => $recordAccount->id)
        );
        if (count($listDeletions) == 0) {
            return;
        }
        return $listDeletions[0];
    }

    /**
     * Setzt den Löschstatus des Accounts zurück
     * @param DragonX_Account_Record_Account $recordAccount
     */
    public function deleteDeletion(DragonX_Account_Record_Account $recordAccount)
    {
    	Zend_Registry::get('DragonX_Storage_Engine')->deleteByConditions(
            new DragonX_Account_Record_Deletion(),
            array('accountid' => $recordAccount->id)
    	);
    }
}
