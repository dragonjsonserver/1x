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
     * Gibt den Account mit der E-Mail Adresse und dem Passwort zurück
     * @param string $emailaddress
     * @param string $password
     * @throws InvalidArgumentException
     * @return DragonX_Account_Record_Account
     */
    public function getAccount($emailaddress, $password)
    {
        $emailaddress = strtolower($emailaddress);
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        $listEmailaddresses = $storage->loadByConditions(
            new DragonX_Emailaddress_Record_Emailaddress(),
            array('emailaddress' => $emailaddress)
        );
        if (count($listEmailaddresses) == 0) {
            throw new InvalidArgumentException('incorrect emailaddress');
        }
        list($recordEmailaddress) = $listEmailaddresses;
        if (!$recordEmailaddress->verifyPassword($password)) {
            throw new InvalidArgumentException('incorrect password');
        }
        if (!$recordAccount = $storage->load(new DragonX_Account_Record_Account($recordEmailaddress->accountid))) {
            throw new Exception('incorrect accountid');
        }

        return $recordAccount;
    }

    /**
     * Verknüpft einen Account mit E-Mail Adresse und Passwort
     * @param DragonX_Account_Record_Account $recordAccount
     * @param string $emailaddress
     * @param string $password
     * @param Zend_Config $configMail
     * @throws InvalidArgumentException
     */
    public function linkAccount(DragonX_Account_Record_Account $recordAccount, $emailaddress, $password, Zend_Config $configMail)
    {
    	$recordEmailaddress = new DragonX_Emailaddress_Record_Emailaddress(
    	    array('accountid' => $recordAccount->id)
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
     * @param DragonX_Account_Record_Account $recordAccount
     */
    public function unlinkAccount(DragonX_Account_Record_Account $recordAccount)
    {
        Zend_Registry::get('DragonX_Storage_Engine')->deleteByConditions(
            new DragonX_Emailaddress_Record_Emailaddress(),
            array('accountid' => $recordAccount->id)
        );
        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Emailaddress_Plugin_UnlinkEmailaddress_Interface',
            array($recordAccount)
        );
    }
}
