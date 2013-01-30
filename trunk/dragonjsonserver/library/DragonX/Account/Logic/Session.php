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
     * @param array $data
     * @return string
     * @throws InvalidArgumentException
     */
    public function loginAccount(Application_Account_Record_Account $recordAccount, $data = array())
    {
        $configEngine = new Dragon_Application_Config('dragonx/session/engine');
        $storageSession = Zend_Registry::get($configEngine->engine);
        $sessionhash = md5($recordAccount->id . '.' . time());
        if ($storageSession instanceof DragonX_Storage_Engine_Memcache) {
            $storageSession->save(new DragonX_Account_Record_Session(
                array(
                    'id' => $sessionhash,
                    'account_id' => $recordAccount->id,
                    'sessionhash' => $sessionhash,
                    'data' => array('account' => $recordAccount->toArray()) + $data,
                ),
                false
            ));
            Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
                'DragonX_Account_Plugin_LoginAccount_Interface',
                array($recordAccount)
            );
            return $sessionhash;
        } else {
            $storageSession->save(new DragonX_Account_Record_Session(array(
                'account_id' => $recordAccount->id,
                'sessionhash' => $sessionhash,
                'data' => Zend_Json::encode(array('account' => $recordAccount->toArray()) + $data),
            )));
            Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
                'DragonX_Account_Plugin_LoginAccount_Interface',
                array($recordAccount)
            );
            return $sessionhash;
        }
    }

    /**
     * Gibt den Account zurück der zum übergebenen Sessionhash hinterlegt ist
     * @param string $sessionhash
     * @return Application_Account_Record_Account
     * @throws InvalidArgumentException
     */
    public function getAccount($sessionhash)
    {
        $configEngine = new Dragon_Application_Config('dragonx/session/engine');
        $storageSession = Zend_Registry::get($configEngine->engine);
        if ($storageSession instanceof DragonX_Storage_Engine_Memcache) {
            $recordSession = $storageSession->load(
                new DragonX_Account_Record_Session($sessionhash)
            );
        } else {
            list ($recordSession) = $storageSession->loadByConditions(
                new DragonX_Account_Record_Session(),
                array('sessionhash' => $sessionhash)
            );
            $recordSession->data = Zend_Json::decode($recordSession->data);
        }
        Zend_Registry::set('recordSession', $recordSession);
        return Zend_Registry::get('DragonX_Storage_Engine')->load(
            new Application_Account_Record_Account($recordSession->account_id)
        );
    }

    /**
     * Meldet den Account ab und entfernt die Session
     * @param string $sessionhash
     */
    public function logoutAccount($sessionhash)
    {
        $recordAccount = $this->getAccount($sessionhash);
        $configEngine = new Dragon_Application_Config('dragonx/session/engine');
        $storageSession = Zend_Registry::get($configEngine->engine);
        if ($storageSession instanceof DragonX_Storage_Engine_Memcache) {
            $storageSession->delete(
                new DragonX_Account_Record_Session($sessionhash)
            );
        } else {
            $storageSession->deleteByConditions(
                new DragonX_Account_Record_Session(),
                array('sessionhash' => $sessionhash)
            );
        }
        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'DragonX_Account_Plugin_LogoutAccount_Interface',
            array($recordAccount)
        );
    }
}
