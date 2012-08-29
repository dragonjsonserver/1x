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
class DragonX_Account_Logic_Credential
{
    /**
     * Lädt den Account und speichert einen neuen Passwort vergessen Hash
     * @param string $identity
     * @param Zend_Config $configMail
     */
    public function request($identity, Zend_Config $configMail)
    {
        $identity = strtolower($identity);
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');

        $listAccounts = $storage->loadByConditions(
            new DragonX_Account_Record_Account(),
            array('identity' => $identity)
        );
        if (count($listAccounts) == 0) {
            throw new InvalidArgumentException('incorrect identity');
        }
        list($recordAccount) = $listAccounts;

        $recordCredential = new DragonX_Account_Record_Credential(array(
            'accountid' => $recordAccount->id,
            'credentialhash' => md5($recordAccount->id . '.' . time()),
            'timestamp' => time(),
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
            ->addTo($identity)
            ->setSubject($configMail->subject)
            ->send();
    }

    /**
     * Setzt das Passwort zurück und entfernt den Passwort vergessen Hash
     * @param string $credentialhash
     * @param string $credential
     */
    public function reset($credentialhash, $credential)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        $listCredentials = $storage->loadByConditions(
            new DragonX_Account_Record_Credential(),
            array('credentialhash' => $credentialhash)
        );
        if (count($listCredentials) == 0) {
            throw new InvalidArgumentException('incorrect credentialhash');
        }
        list($recordCredential) = $listCredentials;

        $recordAccount = new DragonX_Account_Record_Account($recordCredential->accountid);
        $storage->load($recordAccount);
        if (!isset($recordAccount->id)) {
        	throw new Exception('incorrect accountid');
        }
        $recordAccount->credential = md5($credential);
        $storage
            ->save($recordAccount)
            ->delete($recordCredential);

        return $recordAccount;
    }
}
