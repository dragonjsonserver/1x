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
 * Abstrakte Klasse mit den Basismethoden eines Records
 * @property integer $id
 */
abstract class DragonX_Storage_Record_Abstract extends DragonX_Application_Accessor_Abstract
{
	/**
     * @var integer
     */
    protected $_id;

    /**
     * Nimmt die ID, ein Array oder eine andere Eigenschaft als Datenquelle an
     * @param integer|array|DragonX_Application_Accessor_Abstract $data
     * @param boolean $unsetID
     */
    public function __construct($data = array(), $unsetID = true)
    {
        if ($data instanceof DragonX_Application_Accessor_Abstract) {
            $data = $data->toArray();
        }
        if (is_array($data) && $unsetID) {
            unset($data['id']);
        }
        if (is_numeric($data)) {
            $data = array('id' => $data);
        }
    	parent::__construct($data);
    }

    /**
     * Setzt die ID des Records
     * @param integer $id
     */
    protected function setId($id)
    {
        $this->_id = (int)$id;
    }

    /**
     * Gibt die ID des Records zurück
     * @return integer
     */
    protected function getId()
    {
        return $this->_id;
    }

    /**
     * Gibt den Namespace des Records zur Speicherung im Storage zurück
     * @return string
     */
    public function getNamespace()
    {
        return get_class($this);
    }

    /**
     * Erstellt eine Kopie des Records auch zur Datenbank hin
     */
    public function __clone()
    {
        unset($this->_id);
    }
}
