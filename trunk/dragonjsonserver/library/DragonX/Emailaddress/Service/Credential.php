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
 * Serviceklasse für Passwort vergessen Requests
 */
class DragonX_Emailaddress_Service_Credential
{
    /**
     * Ändert das Passwort für den Account
     * @param string $newpassword
     * @dragonx_account_authenticate
     */
    public function changePassword($newpassword)
    {
        $logicCredential = new DragonX_Emailaddress_Logic_Credential();
        $logicCredential->changePassword(Zend_Registry::get('recordAccount'), $newpassword);
    }

    /**
     * Fordert einen neuen Passwort vergessen Hash per E-Mail an
     * @param string $emailaddress
     */
    public function requestCredential($emailaddress)
    {
        $logicCredential = new DragonX_Emailaddress_Logic_Credential();
        $configCredential = new Dragon_Application_Config('dragonx/emailaddress/credential');
        $logicCredential->request($emailaddress, $configCredential->credentialhash);
    }

    /**
     * Setzt Passwort mit dem Passwort vergessen Hash zurück
     * @param string $credentialhash
     * @param string $newpassword
     */
    public function resetCredential($credentialhash, $newpassword)
    {
        $logicCredential = new DragonX_Emailaddress_Logic_Credential();
        $logicCredential->reset($credentialhash, $newpassword);
    }
}
