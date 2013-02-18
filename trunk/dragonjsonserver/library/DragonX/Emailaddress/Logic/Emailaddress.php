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
 * Logikklasse zur Verknüpfung von Accounts mit E-Mail Adresse und Passwort
 */
class DragonX_Emailaddress_Logic_Emailaddress
{
    /**
     * Gibt die Daten der E-Mail Adresse zum Account zurück
     * @param Application_Account_Record_Account $recordAccount
     * @throws InvalidArgumentException
     * @return DragonX_Emailaddress_Record_Emailaddress
     */
    public function getEmailaddress(Application_Account_Record_Account $recordAccount)
    {
        list ($recordEmailaddress) = Zend_Registry::get('DragonX_Storage_Engine')->loadByConditions(
            new DragonX_Emailaddress_Record_Emailaddress(),
            array('account_id' => $recordAccount->id)
        );
        return $recordEmailaddress;
    }

    /**
     * Gibt den Account mit der E-Mail Adresse und dem Passwort zurück
     * @param string $emailaddress
     * @param string $password
     * @throws InvalidArgumentException
     * @return array
     */
    public function getAccount($emailaddress, $password)
    {
        $emailaddress = strtolower($emailaddress);
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        list ($recordEmailaddress) = $storage->loadByConditions(
            new DragonX_Emailaddress_Record_Emailaddress(),
            array('emailaddress' => $emailaddress)
        );
        if (!$recordEmailaddress->verifyPassword($password)) {
            throw new Dragon_Application_Exception_User('incorrect password');
        }

        $recordAccount = $storage->load(new Application_Account_Record_Account($recordEmailaddress->account_id));
        return array($recordAccount, $recordEmailaddress);
    }

    /**
     * Verknüpft einen Account mit E-Mail Adresse und Passwort
     * @param Application_Account_Record_Account $recordAccount
     * @param string $emailaddress
     * @param string $password
     * @param Zend_Config $configMail
     * @throws InvalidArgumentException
     */
    public function linkAccount(Application_Account_Record_Account $recordAccount, $emailaddress, $password, Zend_Config $configMail)
    {
    	$recordEmailaddress = new DragonX_Emailaddress_Record_Emailaddress(
    	    array('account_id' => $recordAccount->id)
    	);
    	$recordEmailaddress
    	    ->validateEmailaddress($emailaddress)
    	    ->hashPassword($password);
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordEmailaddress);

        $logicValidation = new DragonX_Emailaddress_Logic_Validation();
        $logicValidation->request($recordEmailaddress, $configMail);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Emailaddress_Plugin_LinkEmailaddress_Interface',
            array($recordAccount, $recordEmailaddress)
        );
    }

    /**
     * Entfernt die Verknüpfung eines Accounts mit E-Mail Adresse und Passwort
     * @param Application_Account_Record_Account $recordAccount
     */
    public function unlinkAccount(Application_Account_Record_Account $recordAccount)
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');
    	$recordEmailaddress = $this->getEmailaddress($recordAccount);
        $storage->deleteByConditions(
            new DragonX_Emailaddress_Record_Credential(),
            array('emailaddress_id' => $recordEmailaddress->id)
        );
        $storage->deleteByConditions(
            new DragonX_Emailaddress_Record_Validation(),
            array('emailaddress_id' => $recordEmailaddress->id)
        );
        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Emailaddress_Plugin_UnlinkEmailaddress_Interface',
            array($recordAccount)
        );
        $storage->delete($recordEmailaddress);
    }
}
