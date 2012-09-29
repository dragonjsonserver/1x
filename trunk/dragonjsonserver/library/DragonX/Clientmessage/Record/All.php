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
 * Record zur Speicherung einer Clientmessage für Alle
 */
class DragonX_Clientmessage_Record_All extends DragonX_Storage_Record_Created
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $result;

    /**
     * Nimmt die ID, ein Array oder eine andere Eigenschaft als Datenquelle an
     * @param integer|array|DragonX_Clientmessage_Key_Abstract $data
     * @param DragonX_Clientmessage_Key_Abstract $key
     */
    public function __construct($data = array(), DragonX_Clientmessage_Key_Abstract $key = null)
    {
    	parent::__construct($data);
        if (isset($key)) {
        	$this->fromArray(array(
        	    'key' => $key->key,
        	    'result' => Zend_Json::encode($key->toArray())
        	));
        }
    }
}
