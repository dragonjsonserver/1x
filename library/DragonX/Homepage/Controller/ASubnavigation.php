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
 * Controller zum Setzen aller Daten des Layouts
 */
abstract class DragonX_Homepage_Controller_ASubnavigation extends DragonX_Homepage_Controller_Abstract
{
    /**
     * Gibt die Daten für die Subnavigation zurück
     * @return Dragon_Application_Config
     */
    abstract protected function _getSubnavigation();

    /**
     * Gibt das Verzeichnis für die Einträge der Subnavigation zurück
     * @return string
     */
    abstract protected function _getSubnavigationDirectory();

    /**
     * Setzt die Daten für die Subnavigation
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->view->subnavigation = $this->_getSubnavigation();
        $this->view->subnavigationdirectory = $this->_getSubnavigationDirectory();
    }

    /**
     * Action zur Anzeige des aufgerufenen Eintrages der Subnavigation
     */
    public function indexAction()
    {
        if (!isset($this->view->actionname)) {
            $actionname = '';
            foreach ($this->view->subnavigation as $key => $value) {
                if (!is_int($key)) {
                    $actionname = $key;
                    break;
                }
            }
            $this->view->actionname = $actionname;
        }
        $this->render('subnavigation/index', null, true);
    }

    /**
     * Actions zur Ermittlung des aufgerufenen Eintrages der Subnavigation
     * @param string $methodname Der aufgerufene Methodenname
     * @param array $params Die Parameter die beim Aufruf mitgegeben wurden
     */
    public function __call($methodname, $params)
    {
        $this->view->actionname = $this->getRequest()->getActionName();
        $this->indexAction();
    }
}
