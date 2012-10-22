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
 * Controller zur Anzeige von Fehlern während eines Requests
 */
class ErrorController extends DragonX_Homepage_Controller_Abstract
{
    /**
     * Action zur Anzeige von Fehlern während eines Requests
     */
    public function errorAction()
    {
        $error = $this->_getParam('error_handler');
        switch ($error->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Seite nicht verfügbar';
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Interner Anwendungsfehler';
                break;
        }
        $this->view->exception = $error->exception;
    }
}
