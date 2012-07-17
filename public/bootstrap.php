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

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_ENV', getenv('environment'));

require APPLICATION_PATH . '/library/Dragon/Application/Application.php';
$application = new Dragon_Application_Application();
$application
    ->setEnvironment(APPLICATION_ENV)
    ->setTimezone()
    ->addLibrarypaths(array(
        getenv('zendpath'),
        APPLICATION_PATH . '/library',
    ))
    ->initAutoloader()
    ->initPackageRegistry(require APPLICATION_PATH . '/config/packagenamespaces.php')
    ->initPluginRegistry()
    ->bootstrap();
