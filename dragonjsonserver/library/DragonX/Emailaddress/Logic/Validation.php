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
 * Logikklasse zur Verarbeitung von Validierungsabfragen
 */
class DragonX_Emailaddress_Logic_Validation
{
    /**
     * Ändert die E-Mail Adresse trägt eine neue Validierungabfrage ein
     * @param Application_Account_Record_Account $recordAccount
     * @param string $newemailaddress
     * @param Zend_Config $configMail
     * @throws InvalidArgumentException
     */
    public function changeEmailaddress(Application_Account_Record_Account $recordAccount, $newemailaddress, Zend_Config $configMail)
    {
        $logicEmailaddress = new DragonX_Emailaddress_Logic_Emailaddress();
        $recordEmailaddress = $logicEmailaddress->getEmailaddress($recordAccount);
    	$recordEmailaddress->validateEmailaddress($newemailaddress);
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordEmailaddress);

        $logicValidation = new DragonX_Emailaddress_Logic_Validation();
        $logicValidation->request($recordEmailaddress, $configMail);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Emailaddress_Plugin_ChangeEmailaddress_Interface',
            array($recordEmailaddress)
        );
    }
    
    /**
     * Sendet die E-Mail für die Validierungsanfrage
     * @param DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress
     * @param Zend_Config $configMail
     * @param DragonX_Emailaddress_Record_Validation $recordValidation
     */
    private function _sendEmail(DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress, Zend_Config $configMail, DragonX_Emailaddress_Record_Validation $recordValidation)
    {
        $bodytext = str_replace(
            array('%validationhash%', '%validationlink%'),
            array(
                $recordValidation->validationhash,
                BASEURL . 'account/validate?validationhash=' . $recordValidation->validationhash
            ),
            $configMail->bodytext
        );
        $mail = new Zend_Mail();
        $mail
            ->setBodyText($bodytext)
            ->addTo($recordEmailaddress->emailaddress)
            ->setSubject($configMail->subject)
            ->send();
    }

    /**
     * Lädt den Account und speichert einen neuen Hash zur Validierung
     * @param DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress
     * @param Zend_Config $configMail
     */
    public function request(DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress, Zend_Config $configMail)
    {
        $configValidation = new Dragon_Application_Config('dragonx/emailaddress/validation');
        $recordValidation = new DragonX_Emailaddress_Record_Validation(array(
            'emailaddress_id' => $recordEmailaddress->id,
            'validationhash' => $configValidation->{'hashmethod'}($recordEmailaddress),
        ));
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordValidation);
		$this->_sendEmail($recordEmailaddress, $configMail, $recordValidation);
    }

    /**
     * Gibt die Validierungsanfrage der E-Mail Adresse zurück
     * @param DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress
     * @return DragonX_Emailaddress_Record_Validation
     */
    public function getValidation($recordEmailaddress)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        list ($recordValidation) = $storage->loadByConditions(
            new DragonX_Emailaddress_Record_Validation(),
            array('emailaddress_id' => $recordEmailaddress->id)
        );
        return $recordValidation;
    }

    /**
     * Sendet die Validierungsanfrage erneut zu
     * @param DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress
     * @param Zend_Config $configMail
     */
    public function resendValidation(DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress, Zend_Config $configMail)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        list ($recordValidation) = $storage->loadByConditions(
        	new DragonX_Emailaddress_Record_Validation(),
        	array('emailaddress_id' => $recordEmailaddress->id)
        );
        $configValidation = new Dragon_Application_Config('dragonx/emailaddress/validation');
        if (time() - $configValidation->resendinterval < $recordValidation->modified) {
        	throw new Dragon_Application_Exception_User('resendinterval not reached');
        }
        $storage->save($recordValidation);
		$this->_sendEmail($recordEmailaddress, $configMail, $recordValidation);
    }

    /**
     * Entfernt die Validierungsabfrage und schaltet somit den Account frei
     * @param string $validationhash
     */
    public function validate($validationhash)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        list ($recordValidation) = $storage->loadByConditions(
            new DragonX_Emailaddress_Record_Validation(),
            array('validationhash' => $validationhash)
        );

        $recordEmailaddress = $storage->load(new DragonX_Emailaddress_Record_Emailaddress($recordValidation->emailaddress_id));
        $storage->delete($recordValidation);

        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Emailaddress_Plugin_ValidateEmailaddress_Interface',
            array($recordEmailaddress)
        );

        $recordAccount = $storage->load(new Application_Account_Record_Account($recordEmailaddress->account_id));
        return $recordAccount;
    }
}
