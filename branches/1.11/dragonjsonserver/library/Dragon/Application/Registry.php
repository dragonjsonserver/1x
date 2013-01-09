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
 * Klasse zur Erweiterung der Zend Registry um Callbackmethoden
 */
class Dragon_Application_Registry extends Zend_Registry
{
	/**
	 * @var array
	 */
	private $_callbacks = array();

    /**
     * Setzt die Callback die beim Aufruf des Index verwendet wird
     * @param string $index
     * @param function $callback
     */
    public function setCallback($index, $callback)
    {
        $this->_callbacks[$index] = $callback;
    }

    /**
     * Prüft ob der Index gesetzt wurde oder eine Callback existiert
     * @param string $index
     * @return boolean
     */
    public function offsetExists($index)
    {
        return isset($this->_callbacks[$index]) || parent::offsetExists($index);
    }

    /**
     * Gibt den vorhandenen oder durch die Callback gesetzten Index zurück
     * @param string $index
     * @return mixed
     */
    public function offsetGet($index)
    {
    	if (!parent::offsetExists($index)) {
    		if (!isset($this->_callbacks[$index])) {
    			throw new Dragon_Application_Exception_System('incorrect index');
    		}
    		parent::offsetSet($index, $this->_callbacks[$index]());
    	}
    	return parent::offsetGet($index);
    }
}
