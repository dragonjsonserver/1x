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
 * Controller zur Anzeige der Lizenzbestimmungen der Anwendung
 */
class LicenseController extends DragonX_Homepage_Controller_Abstract
{
    /**
     * Action zur Anzeige der Lizenzbestimmungen der Anwendung
     */
    public function indexAction()
    {
        $configLicense = new Dragon_Application_Config('dragonx/homepage/license');
        if (!isset($configLicense->license)) {
        	throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $this->getRequest()->getControllerName() . ')');
        }
        $this->render($configLicense->license);
    }
}
