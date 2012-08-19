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
 * Abstrakte Klasse zur Implementierung eines Keys mit seinen Daten
 */
class DragonX_Clientmessage_Key_Abstract extends Dragon_Application_Accessor
{
	/**
	 * @var string
	 */
	protected $_key;

    /**
     * Gibt den Key für die Clientnachricht zurück
     * @return string
     */
	protected function getKey()
	{
		return $this->_key;
	}

    /**
     * Gibt alle Attribute der Eigenschaft als Array zurück
     * @return array
     */
    public function toArray()
    {
    	$array = parent::toArray();
    	unset($array['key']);
    	return $array;
    }
}
