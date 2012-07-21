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
abstract class DragonX_Homepage_Controller_Abstract extends Zend_Controller_Action
{
    /**
     * Setzt alle Daten des Layouts aus den Einstellungsdateien
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->view->application = new Dragon_Application_Config('dragon/application/application');
        $this->view->navigation = new Dragon_Application_Config('dragonx/homepage/navigation');
        $this->view->controllername = $this->getRequest()->getControllerName();
    }
}
