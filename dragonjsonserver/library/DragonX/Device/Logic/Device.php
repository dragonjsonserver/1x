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
     * Fügt dem Account ein Gerät hinzu
     * @param Application_Account_Record_Account $recordAccount
     * @param string $platform
     * @param string $name
     * @param string $pushnotification_platform
     * @param string $pushnotification_token
     */
    public function addDevice($recordAccount, $platform, $name, $pushnotification_platform, $pushnotification_token)
    {
    	Zend_Registry::get('DragonX_Storage_Engine')->save(
    	    new DragonX_Device_Record_Device(array(
    	        'accountid' => $recordAccount->id,
    	        'platform' => $platform,
    	        'name' => $name,
    	        'pushnotification' => array(
    	            'platform' => $pushnotification_platform,
    	            'token' => $pushnotification_token,
    	        ),
    	    ))
    	);
    }
}
