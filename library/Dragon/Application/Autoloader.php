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
 * Autoloader zum Nachladen von Klassen aus der Library
 */
class Dragon_Application_Autoloader
{
    /**
     * Registriert die Methode zum Nachladen von Klassen aus der Library
     */
    public function __construct()
    {
        spl_autoload_register(array($this, '_autoload'));
    }

    /**
     * Autoloadermethode zum Nachladen von Klassen aus der Library
     * @param string $classname
     */
    private function _autoload($classname)
    {
        $classnamearray = explode('_', $classname, 3);
        if (count($classnamearray) < 3) {
            return;
        }
        list ($packagenamespace, $packagename) = $classnamearray;
        $packageregistry = Zend_Registry::get('Dragon_Package_Registry');
        $packagenamespaces = $packageregistry->getPackagenamespaces();
        if (!isset($packagenamespaces[$packagenamespace])
            || (
               !isset($packagenamespaces[$packagenamespace][$packagename])
               &&
               !in_array($packagename, $packagenamespaces[$packagenamespace], true)
            )) {
            return;
        }
        require str_replace('_', '/', $classname) . '.php';
    }
}
