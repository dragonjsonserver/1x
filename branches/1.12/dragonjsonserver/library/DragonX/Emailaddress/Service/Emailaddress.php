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
 * Serviceklasse zur Verknüpfung von Accounts mit E-Mail Adresse und Passwort
 */
class DragonX_Emailaddress_Service_Emailaddress
{
    /**
     * Meldet den Account mit der E-Mail Adresse und dem Passwort an
     * @param string $emailaddress
     * @param string $password
     * @throws InvalidArgumentException
     * @return array
     */
    public function loginAccount($emailaddress, $password)
    {
        $logicEmailaddress = new DragonX_Emailaddress_Logic_Emailaddress();
        $logicSession = new DragonX_Account_Logic_Session();
        $sessionhash = $logicSession->loginAccount(
            $logicEmailaddress->getAccount($emailaddress, $password)
        );
        return array('sessionhash' => $sessionhash);
    }

    /**
     * Verknüpft einen Account mit E-Mail Adresse und Passwort
     * @param string $emailaddress
     * @param string $password
     * @throws InvalidArgumentException
     * @dragonx_account_authenticate
     */
    public function linkAccount($emailaddress, $password)
    {
        $logicEmailaddress = new DragonX_Emailaddress_Logic_Emailaddress();
        $configValidation = new Dragon_Application_Config('dragonx/emailaddress/validation');
        $logicEmailaddress->linkAccount(
            Zend_Registry::get('recordAccount'),
            $emailaddress,
            $password,
            $configValidation->validationhash
        );
    }

    /**
     * Entfernt die Verknüpfung eines Accounts mit E-Mail Adresse und Passwort
     * @dragonx_account_authenticate
     */
    public function unlinkAccount()
    {
        $logicEmailaddress = new DragonX_Emailaddress_Logic_Emailaddress();
        $logicEmailaddress->unlinkAccount(Zend_Registry::get('recordAccount'));
    }
}
