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
 * Ausnahmeklasse mit der Möglichkeit zusätzliche Daten anzugeben
 */
class Dragon_Application_Exception extends Zend_Exception
{
	/**
     * @var array
     */
    private $_data = array();

    /**
     * Erstellt die Ausnahme mit der Möglichkeit zusätzliche Daten anzugeben
     * @param string $message
     * @param array $data
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = '', array $data = array(), $code = 0, Exception $previous = null)
    {
    	$this->_data = $data;
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Gibt die Daten zurück die zusätzlich zur Ausnahme angegeben wurden
     * @return array
     */
    public function getData()
    {
    	return $this->_data;
    }
}
