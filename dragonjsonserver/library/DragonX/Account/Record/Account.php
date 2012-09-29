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
 * Record zur Speicherung der Daten zu einem Account
 */
class DragonX_Account_Record_Account extends DragonX_Storage_Record_CreatedModified
{
    public static function newInstance($data = array())
    {
    	if (class_exists('Application_Account_Record_Account')) {
    		return new Application_Account_Record_Account($data);
    	}
    	return new DragonX_Account_Record_Account($data);
    }
}
