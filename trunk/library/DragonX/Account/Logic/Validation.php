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
class DragonX_Account_Logic_Validation
{
    /**
     * Lädt den Account und speichert einen neuen Hash zur Validierung
     * @param DragonX_Account_Record_Account $recordAccount
     * @param Zend_Config $configMail
     */
    public function request(DragonX_Account_Record_Account $recordAccount, Zend_Config $configMail)
    {
        $recordValidation = new DragonX_Account_Record_Validation(array(
            'accountid' => $recordAccount->id,
            'validationhash' => md5($recordAccount->id . '.' . time()),
            'timestamp' => time(),
        ));
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordValidation);

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
            ->addTo($recordAccount->identity)
            ->setSubject($configMail->subject)
            ->send();
    }

    /**
     * Entfernt die Validierungsabfrage und schaltet somit den Account frei
     * @param string $validationhash
     */
    public function validate($validationhash)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        $listValidations = $storage->loadByConditions(
            new DragonX_Account_Record_Validation(),
            array('validationhash' => $validationhash)
        );
        if (count($listValidations) == 0) {
            throw new InvalidArgumentException('incorrect validationhash');
        }
        list($recordValidation) = $listValidations;

        if (!$recordAccount = $storage->load(new DragonX_Account_Record_Account($recordValidation->accountid))) {
            throw new Exception('incorrect accountid');
        }
        $storage->delete($recordValidation);

        return $recordAccount;
    }
}
