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
 * Statische Klasse zur Verwaltung der Plugins aller Pakete
 */
class Dragon_Plugin_Registry
{
    /**
     * @var array
     */
    private $_plugins = array();

    /**
     * Fügt mehrere Plugins hinzu und registriert diese bei allen Schnittstellen
     * @param array $plugins
     */
    public function __construct(array $plugins)
    {
        foreach ($plugins as $plugin) {
            $reflectionClass = new ReflectionClass($plugin);
            foreach ($reflectionClass->getInterfaceNames() as $interfacename) {
                if (!isset($this->_plugins[$interfacename])) {
                    $this->_plugins[$interfacename] = array();
                }
                $this->_plugins[$interfacename][] = $plugin;
            }
        }
    }

    /**
     * Gibt alle Plugins der übergebenen Schnittstelle zurück
     * @param string $interfacename
     * @return array
     */
    public function getPlugins($interfacename)
    {
        if (!isset($this->_plugins[$interfacename])) {
            return array();
        }
        $plugins = array();
        foreach ($this->_plugins[$interfacename] as &$plugin) {
            if (!is_object($plugin)) {
                $plugin = new $plugin();
            }
            $plugins[] = $plugin;
        }
        unset($plugin);
        return $plugins;
    }

    /**
     * Führt die Methode mit den Paremetern aller Plugins der Schnittstelle aus
     * @param string $interfacename
     * @param array $params
     * @param string $methodname
     * @return Dragon_Plugin_Registry
     */
    public function invoke($interfacename, $params = array(), $methodname = null)
    {
        if (!isset($methodname)) {
            $reflectionClass = new ReflectionClass($interfacename);
            $reflectionMethods = $reflectionClass->getMethods();
            if (count($reflectionMethods) == 0) {
                return;
            }
            $methodname = $reflectionMethods[0]->name;
        }
        $plugins = $this->getPlugins($interfacename);
        foreach ($plugins as $plugin) {
            call_user_func_array(array($plugin, $methodname), $params);
        }
        return $this;
    }
}
