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
class DragonX_Account_Logic_Account
{
    /**
     * Registriert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @return integer
     */
    public function registerAccount($identity, $credential)
    {
        $identity = strtolower($identity);
    	$validatorEmailAddress = new Zend_Validate_EmailAddress();
    	if (!$validatorEmailAddress->isValid($identity)) {
    		throw new InvalidArgumentException('invalid identity');
    	}
    	$recordAccount = new DragonX_Account_Record_Account(array(
    	    'identity' => $identity,
    	    'credential' => md5($credential),
    	));
    	Zend_Registry::get('DragonX_Storage_Engine')->save($recordAccount);
    	return $recordAccount->id;
    }

    /**
     * Authentifiziert einen Account mit der Identity und dem Credential
     * @param string $identity
     * @param string $credential
     * @return integer
     * @throws InvalidArgumentException
     */
    public function authenticateAccount($identity, $credential)
    {
        $identity = strtolower($identity);
    	$listAccounts = Zend_Registry::get('DragonX_Storage_Engine')->loadByConditions(
    	    new DragonX_Account_Record_Account(),
    	    array('identity' => $identity, 'credential' => md5($credential))
    	);
        if (count($listAccounts) == 0) {
            throw new InvalidArgumentException('incorrect authenticate');
        }
        return $listAccounts[0];
    }

    /**
     * Meldet einen Account mit der Identity und dem Credential an
     * @param string $identity
     * @param string $credential
     * @throws InvalidArgumentException
     */
    public function loginAccount($identity, $credential)
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $sessionNamespace->recordAccount = $this->authenticateAccount($identity, $credential);
    }

    /**
     * Meldet den aktuell eingeloggten Account wieder ab
     */
    public function logoutAccount()
    {
        $sessionNamespace = new Zend_Session_Namespace();
        $sessionNamespace->unsetAll();
    }
}