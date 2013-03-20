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
 * Logikklasse zur Verarbeitung von Passwort vergessen Abfragen
 */
class DragonX_Emailaddress_Logic_Credential
{
    /**
     * Ändert das Passwort für den Account
     * @param Application_Account_Record_Account $recordAccount
     * @param string $newpassword
     */
    public function changePassword(Application_Account_Record_Account $recordAccount, $newpassword)
    {
        $logicEmailaddress = new DragonX_Emailaddress_Logic_Emailaddress();
        $recordEmailaddress = $logicEmailaddress->getEmailaddress($recordAccount);
        $recordEmailaddress->validatePassword($newpassword);
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordEmailaddress);
    }

    /**
     * Lädt den Account und speichert einen neuen Passwort vergessen Hash
     * @param string $emailaddress
     * @param Zend_Config $configMail
     */
    public function request($emailaddress, Zend_Config $configMail)
    {
        $emailaddress = strtolower($emailaddress);
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        list ($recordEmailaddress) = $storage->loadByConditions(
            new DragonX_Emailaddress_Record_Emailaddress(),
            array('emailaddress' => $emailaddress)
        );

        $configCredential = new Dragon_Application_Config('dragonx/emailaddress/credential');
        $hashmethod = $configValidation->hashmethod;
        $recordCredential = new DragonX_Emailaddress_Record_Credential(array(
            'emailaddress_id' => $recordEmailaddress->id,
            'credentialhash' => $hashmethod($recordEmailaddress),
        ));
        $storage->save($recordCredential);

        $bodytext = str_replace(
            array('%credentialhash%', '%credentiallink%'),
            array(
                $recordCredential->credentialhash,
                BASEURL . 'credential/showreset?credentialhash=' . $recordCredential->credentialhash
            ),
            $configMail->bodytext
        );
        $mail = new Zend_Mail();
        $mail
            ->setBodyText($bodytext)
            ->addTo($emailaddress)
            ->setSubject($configMail->subject)
            ->send();
    }

    /**
     * Setzt das Passwort zurück und entfernt den Passwort vergessen Hash
     * @param string $credentialhash
     * @param string $newpassword
     * @return DragonX_Emailaddress_Record_Emailaddress
     */
    public function reset($credentialhash, $newpassword)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        list ($recordCredential) = $storage->loadByConditions(
            new DragonX_Emailaddress_Record_Credential(),
            array('credentialhash' => $credentialhash)
        );

        $recordEmailaddress = $storage->load(new DragonX_Emailaddress_Record_Emailaddress($recordCredential->emailaddress_id));
        $recordEmailaddress->hashPassword($newpassword);
        $storage->save($recordEmailaddress);
        $storage->delete($recordCredential);

        $recordAccount = $storage->load(new Application_Account_Record_Account($recordEmailaddress->account_id));
        return $recordAccount;
    }
}
