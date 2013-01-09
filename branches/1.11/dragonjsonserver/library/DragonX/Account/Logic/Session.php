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
 * Logikklasse zur Verwaltung einer Session
 */
class DragonX_Account_Logic_Session
{
    /**
     * Meldet einen Account an und erstellt für diesen eine neue Session
     * @param Application_Account_Record_Account $recordAccount
     * @return string
     * @throws InvalidArgumentException
     */
    public function loginAccount(Application_Account_Record_Account $recordAccount)
    {
        $recordSession = new DragonX_Account_Record_Session(array(
            'account_id' => $recordAccount->id,
            'sessionhash' => md5($recordAccount->id . '.' . time()),
        ));
        Zend_Registry::get('DragonX_Storage_Engine')->save($recordSession);
        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_LoginAccount_Interface',
            array($recordAccount)
        );
        return $recordSession->sessionhash;
    }

    /**
     * Gibt den Account zurück der zum übergebenen Sessionhash hinterlegt ist
     * @param string $sessionhash
     * @return Application_Account_Record_Account
     * @throws InvalidArgumentException
     */
    public function getAccount($sessionhash)
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');
        list ($recordAccount) = $storage->loadBySqlStatement(
            new Application_Account_Record_Account(),
              "SELECT `account`.* FROM `application_account_record_account` AS `account` "
            . "INNER JOIN `dragonx_account_record_session` AS `session` ON `session`.`account_id` = `account`.`id` "
            . "WHERE `session`.`sessionhash` = :sessionhash",
            array('sessionhash' => $sessionhash)
        );
        return $recordAccount;
    }

    /**
     * Meldet den Account ab und entfernt die Session
     * @param string $sessionhash
     */
    public function logoutAccount($sessionhash)
    {
        $recordAccount = $this->getAccount($sessionhash);
        Zend_Registry::get('DragonX_Storage_Engine')->deleteByConditions(
            new DragonX_Account_Record_Session(),
            array('sessionhash' => $sessionhash)
        );
        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_LogoutAccount_Interface',
            array($recordAccount)
        );
    }
}
