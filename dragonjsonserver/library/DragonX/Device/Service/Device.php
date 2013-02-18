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
 * Serviceklasse zur Verwaltung von Geräten
 */
class DragonX_Device_Service_Device
{
    /**
     * Gibt die Daten der Geräte zum Account zurück
     * @return array
     * @dragonx_account_authenticate
     */
    public function getDevices()
    {
    	$logicDevice = new DragonX_Device_Logic_Device();
    	return $logicDevice->getDevices(Zend_Registry::get('recordAccount'));
    }

    /**
     * Gibt den Account mit den übergebenen Anmeldedaten des Gerätes zurück
     * @param string $platform
     * @param object $credentials
     * @return array
     */
    public function loginAccount($platform = 'test', array $credentials = array('a' => '', 'b' => '', 'c' => ''))
    {
        $logicDevice = new DragonX_Device_Logic_Device();
        $logicSession = new DragonX_Account_Logic_Session();
        list ($recordAccount) = $logicDevice->getAccount($platform, $credentials);
        $sessionhash = $logicSession->loginAccount($recordAccount);
        return array('sessionhash' => $sessionhash);
    }

    /**
     * Verknüpft einen Account mit den übergebenen Anmeldedaten des Gerätes
     * @param string $platform
     * @param object $credentials
     * @param string $devicename
     * @param string $locale
     * @dragonx_account_authenticate
     */
    public function linkAccount($platform = 'test', array $credentials = array('a' => '', 'b' => '', 'c' => ''), $devicename, $locale)
    {
        $logicDevice = new DragonX_Device_Logic_Device();
        $logicDevice->linkAccount(Zend_Registry::get('recordAccount'), $platform, $credentials, $devicename, $locale);
    }

    /**
     * Entfernt die Verknüpfung eines Accounts mit einem Gerät
     * @param integer $device_id
     * @dragonx_account_authenticate
     */
    public function unlinkAccount($device_id)
    {
        $logicDevice = new DragonX_Device_Logic_Device();
        $logicDevice->unlinkAccount(Zend_Registry::get('recordAccount'), $device_id);
    }

    /**
     * Aktualisiert die Sprache des übergebenen Gerätes
     * @param integer $device_id
     * @param string $locale
     * @dragonx_account_authenticate
     */
    public function updateLocale($device_id, $locale)
    {
        $logicDevice = new DragonX_Device_Logic_Device();
        $logicDevice->updateLocale(Zend_Registry::get('recordAccount'), $device_id, $locale);
    }
}
