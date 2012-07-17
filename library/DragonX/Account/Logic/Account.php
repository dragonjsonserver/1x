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
class DragonX_Account_Logic_Account extends DragonX_Database_Logic_Abstract
{
    /**
     * Registriert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @return integer
     */
    public function registerAccount($identity, $credential)
    {
        $modelAccount = new DragonX_Account_Model_Account();
        return $modelAccount->registerAccount($identity, md5($credential));
    }

    /**
     * Authentifiziert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @return integer
     */
    public function authenticateAccount($identity, $credential)
    {
        $modelAccount = new DragonX_Account_Model_Account();
        $rows = $modelAccount->authenticateAccount($identity, md5($credential));
        if (count($rows) == 0) {
            throw new InvalidArgumentException('incorrect authenticate');
        }
        return $rows[0]['accountid'];
    }
}
