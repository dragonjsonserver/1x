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
 * Serviceklasse zur Registrierung von Accounts
 */
class DragonX_Account_Service_Account
{
    /**
     * Registriert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @return array
     */
    public function registerAccount($identity, $credential)
    {
        $logicAccount = new DragonX_Account_Logic_Account();
        $accountid = $logicAccount->registerAccount($identity, $credential);
        return array('accountid' => $accountid);
    }

    /**
     * Gibt die AccountID der Identity und dem Credential zurück
     * @return array
     * @dragonx_account
     */
    public function authenticateAccount()
    {
        return array('accountid' => Zend_Registry::get('recordAccount')->id);
    }
}
