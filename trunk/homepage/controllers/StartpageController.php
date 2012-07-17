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
 * Controller zur Anzeige der Informationen und Neuigkeiten der Startseite
 */
class StartpageController extends DragonX_Homepage_Controller
{
    /**
     * Action zur Anzeige der Informationen und Neuigkeiten der Startseite
     */
    public function indexAction()
    {
        $this->view->startpage = new Dragon_Application_Config('dragonx/homepage/startpage');
        $news = new Dragon_Application_Config('dragonx/homepage/news');
        $this->view->news = $news;
        if (!isset($this->view->actionname)) {
            $this->view->actionname = $news->amount;
        }
        $this->render('index');
    }

    /**
     * Action zur Anzeige aller Neuigkeiten der Startseite
     */
    public function allAction()
    {
        $this->view->actionname = 'all';
        $this->indexAction();
    }
}
