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
 * Plugin für Clientnachrichten die im aktuellen Request erstellt wurden
 */
class DragonX_Clientmessage_Plugin_ActualRequest implements DragonX_Clientmessage_Plugin_Source_Interface
{
	/**
	 * @var array
	 */
	private $_clientmessages = array();

    /**
     * Fügt der aktuellen Response eine Clientmessage hinzu
     * @param DragonX_Clientmessage_Record_All $clientmessage
     * @return DragonX_Clientmessage_Plugin_ActualRequest
     */
    public function addClientmessage(DragonX_Clientmessage_Record_All $clientmessage)
    {
    	$clientmessage->created = time();
    	$this->_clientmessages[] = $clientmessage;
    	return $this;
    }

    /**
     * Wird beim Abholen der Clientnachrichten aufgerufen
     * @param integer $lastResponse
     * @param integer $actualResponse
     * @return RecordList
     */
    public function getClientmessages($lastResponse, $actualResponse)
    {
        return $this->_clientmessages;
    }
}
