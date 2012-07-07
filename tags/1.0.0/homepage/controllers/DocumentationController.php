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
 * Controller zur Anzeige der Dokumentation zur Anwendung
 */
class DocumentationController extends DragonX_Homepage_Controller
{
	/**
	 * Action zur Anzeige der der Dokumentation zur Anwendung
	 */
    public function indexAction()
    {
        $documentation = new Dragon_Application_Config('dragonx/homepage/documentation');
        $this->view->documentation = $documentation;
        if (!isset($this->view->actionname)) {
        	$actionname = '';
	        foreach ($documentation as $key => $value) {
	            if (!is_int($key)) {
	                $actionname = $key;
	                break;
	            }
	        }
	        $this->view->actionname = $actionname;
        }
        $this->render('index');
    }

    /**
     * Actions zur Ermittlung der aufgerufenen Dokumentationsseite
     * @param string $methodname Der aufgerufene Methodenname
     * @param array $params Die Parameter die beim Aufruf mitgegeben wurden
     */
    public function __call($methodname, $params)
    {
    	$this->view->actionname = $this->getRequest()->getActionName();
    	$this->indexAction();
    }
}
