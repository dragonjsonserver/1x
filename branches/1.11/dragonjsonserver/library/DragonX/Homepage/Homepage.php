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
 * Klasse zur Verarbeitung eines Aufruf der Homepage über Zend MVC
 */
class DragonX_Homepage_Homepage
{
    /**
     * Verarbeitet einen Aufruf der Homepage über Zend MVC
     * @return Dragon_Application_Application
     */
    public function dispatch()
    {
        $application = new Zend_Application(
            APPLICATION_ENV,
            DRAGONJSONSERVER_PATH . '/config/dragonx/homepage/application.ini'
        );
        if (Zend_Registry::isRegistered('Dragon_Repository_Registry')) {
	        $frontController = Zend_Controller_Front::getInstance();
        	foreach (Zend_Registry::get('Dragon_Repository_Registry')->getRepositories() as $repositoryname => $directorypath) {
                $administrationpath = $directorypath . '/modules/administration/controllers';
                if (is_dir($administrationpath)) {
                    $frontController->addControllerDirectory($administrationpath, $repositoryname . '_administration');
                }
                $homepagepath = $directorypath . '/modules/homepage/controllers';
                if (is_dir($homepagepath)) {
                    $frontController->addControllerDirectory($homepagepath, $repositoryname . '_homepage');
                }
        	}
        }
        $application
            ->bootstrap()
            ->run();
        return $this;
    }
}
