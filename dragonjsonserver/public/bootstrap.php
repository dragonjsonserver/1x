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

define('DRAGONJSONSERVER_PATH', realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_PATH', DRAGONJSONSERVER_PATH . '/../application');
if ($environment = getenv('environment')) {
	define('APPLICATION_ENV', $environment);
} else {
	define('APPLICATION_ENV', 'development');
}
define('BASEURL', 'http://' . $_SERVER["SERVER_NAME"] . ':8888/DragonJsonServer/dragonjsonserver/public/');

if (!$zendpath = getenv('zendpath')) {
    $zendpath = DRAGONJSONSERVER_PATH . '/../library';
}
if (false) {
    $repositoriespath = APPLICATION_PATH . '/config/repositories.php';
    if (is_file($repositoriespath)) {
    	$repositories = require $repositoriespath;
    } else {
    	$repositories = array('application' => APPLICATION_PATH);
    }
	$packagenamespaces = require APPLICATION_PATH . '/config/packagenamespaces.php';

	$bootstrappath = APPLICATION_PATH . '/bootstrap.php';
    if (is_file($bootstrappath)) {
        require $bootstrappath;
    }
} else {
	$repositories = false;
    $packagenamespaces = require DRAGONJSONSERVER_PATH . '/config/packagenamespaces.php';
}

require DRAGONJSONSERVER_PATH . '/library/Dragon/Application/Application.php';
$application = new Dragon_Application_Application();
$application
    ->setEnvironment(APPLICATION_ENV)
    ->setTimezone()
    ->addLibrarypaths(array(
        $zendpath,
        DRAGONJSONSERVER_PATH . '/library',
    ))
    ->initAutoloader()
    ->initPackageRegistry($packagenamespaces);
if ($repositories) {
    $application->initRepositoryRegistry($repositories);
}
$application
    ->initPluginRegistry()
    ->bootstrap();
