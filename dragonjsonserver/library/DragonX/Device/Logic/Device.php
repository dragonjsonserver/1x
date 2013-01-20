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
 * Logikklasse zur Verwaltung von Geräten
 */
class DragonX_Device_Logic_Device
{
    /**
     * Gibt die Daten der Geräte zum Account zurück
     * @param Application_Account_Record_Account $recordAccount
     * @return DragonX_Storage_RecordList
     */
    public function getDevices(Application_Account_Record_Account $recordAccount)
    {
        return Zend_Registry::get('DragonX_Storage_Engine')->loadByConditions(
            new DragonX_Device_Record_Device(),
            array('account_id' => $recordAccount->id)
        );
    }

    /**
     * Gibt die Anmeldedaten für die Platform zurück
     * @param string $platform
     * @param array $credentials
     * @return array
     */
    private function _getCredentials($platform, array $credentials)
    {
        $configCredential = new Dragon_Application_Config('dragonx/device/credential');
        if (!isset($configCredential->$platform)) {
        	throw new Dragon_Application_Exception_User('incorrect platform');
        }
        $configCredentialArray = $configCredential->$platform->toArray();
        foreach ($credentials as $key => $value) {
        	if (!in_array($key, $configCredentialArray)) {
        		unset($credentials[$key]);
        	}
        }
        if (count($credentials) == 0) {
        	throw new Dragon_Application_Exception_User('incorrect credentials');
        }
    	return $credentials;
    }

    /**
     * Gibt den Account mit den übergebenen Anmeldedaten des Gerätes zurück
     * @param string $platform
     * @param array $credentials
     * @return Application_Account_Record_Account
     */
    public function getAccount($platform, array $credentials)
    {
    	$credentials = $this->_getCredentials($platform, $credentials);
    	$conditions = array();
    	$params = array();
    	$index = 0;
    	foreach ($credentials as $key => $value) {
    		$conditions[] = "(credential.key = :credential_key_" . $index . " AND credential.value = :credential_value_" . $index . ")";
    		$params['credential_key_' . $index] = $key;
    		$params['credential_value_' . $index] = $value;
            ++$index;
    	}
    	list ($recordAccount) = Zend_Registry::get('DragonX_Storage_Engine')->loadBySqlStatement(
            new Application_Account_Record_Account(),
            "SELECT account.*, count(*) AS amount
            FROM application_account_record_account AS account
            INNER JOIN dragonx_device_record_device AS device ON device.account_id = account.id
            INNER JOIN dragonx_device_record_credential AS credential ON credential.device_id = device.id
            WHERE
                " . implode(' OR ', $conditions) . "
            GROUP BY
                device.id
            HAVING amount = :amount",
            array('amount' => count($credentials)) + $params
    	);
    	return $recordAccount;
    }

    /**
     * Verknüpft einen Account mit den übergebenen Anmeldedaten des Gerätes
     * @param Application_Account_Record_Account $recordAccount
     * @param string $platform
     * @param array $credentials
     * @param string $devicename
     * @param string $locale
     */
    public function linkAccount(Application_Account_Record_Account $recordAccount, $platform, array $credentials, $devicename, $locale)
    {
        $credentials = $this->_getCredentials($platform, $credentials);
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        $recordDevice = new DragonX_Device_Record_Device(array(
            'account_id' => $recordAccount->id,
            'platform' => $platform,
            'devicename' => $devicename,
            'locale' => array(
		        'register' => $locale,
		        'actual' => $locale,
            ),
        ));
        $storage->save($recordDevice);
        $listCredential = new DragonX_Storage_RecordList();
        foreach ($credentials as $key => $value) {
        	$listCredential[] = new DragonX_Device_Record_Credential(array(
	            'device_id' => $recordDevice->id,
	            'key' => $key,
	            'value' => $value,
        	));
        }
        $storage->saveList($listCredential);
    }

    /**
     * Entfernt die Verknüpfung eines Accounts mit einem Gerät
     * @param Application_Account_Record_Account $recordAccount
     * @param integer $device_id
     */
    public function unlinkAccount(Application_Account_Record_Account $recordAccount, $device_id)
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');
        list ($recordDevice) = $storage->loadByConditions(
            new DragonX_Device_Record_Device(),
            array('account_id' => $recordAccount->id, 'id' => $device_id)
        );
        $storage->deleteByConditions(
            new DragonX_Device_Record_Credential(),
            array('device_id' => $recordDevice->id)
        );
        $storage->delete($recordDevice);
    }

    /**
     * Aktualisiert die Sprache des übergebenen Gerätes
     * @param Application_Account_Record_Account $recordAccount
     * @param integer $device_id
     * @param string $locale
     */
    public function updateLocale(Application_Account_Record_Account $recordAccount, $device_id, $locale)
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');
    	list ($recordDevice) = $storage->loadByConditions(
            new DragonX_Device_Record_Device(),
            array('account_id' => $recordAccount->id, 'id' => $device_id)
    	);
    	$recordDevice->locale['actual'] = $locale;
    	$storage->save($recordDevice);
    }
}
