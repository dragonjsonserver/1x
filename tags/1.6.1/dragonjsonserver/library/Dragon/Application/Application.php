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
 * Klasse zur Behandlung von JsonRPC Requests und Aufrufe der Homepage
 */
class Dragon_Application_Application
{
    /**
     * Setzt den Umgebungswert zur Bestimmung der Anzeige von Fehlermeldungen
     * @param string $environment
     * @return Dragon_Application_Application
     */
    public function setEnvironment($environment)
    {
        if ($environment == 'development') {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', 1);
        }
        return $this;
    }

    /**
     * Setzt die Einstellung der Zeitzonen für Zeit- und Datumsfunktionen
     * @param string $timezone
     * @return Dragon_Application_Application
     */
    public function setTimezone($timezone = 'Europe/Berlin')
    {
        date_default_timezone_set($timezone);
        return $this;
    }

    /**
     * Fügt den Librarypfad zum Include Path hinzu
     * @param string $librarypath
     * @return Dragon_Application_Application
     */
    public function addLibrarypath($librarypath)
    {
        set_include_path(realpath($librarypath) . PATH_SEPARATOR . get_include_path());
        return $this;
    }

    /**
     * Fügt die Librarypfade zum Include Path hinzu
     * @param array $librarypaths
     * @return Dragon_Application_Application
     */
    public function addLibrarypaths(array $librarypaths)
    {
        foreach ($librarypaths as $librarypath) {
            $this->addLibrarypath($librarypath);
        }
        return $this;
    }

    /**
     * Initialisiert Zend und Dragon Autoloader zum Nachladen von Klassen
     * @return Dragon_Application_Application
     */
    public function initAutoloader()
    {
        require 'Zend/Loader/Autoloader.php';
        Zend_Loader_Autoloader::getInstance();
        require 'Dragon/Application/Autoloader.php';
        new Dragon_Application_Autoloader();
        return $this;
    }

    /**
     * Initialisiert die Package Registry zum Verwalten von Paketen
     * @param array $packagenamespaces
     * @return Dragon_Application_Application
     */
    public function initPackageRegistry(array $packagenamespaces)
    {
        require 'Dragon/Package/Registry.php';
        $packageregistry = new Dragon_Package_Registry($packagenamespaces);
        Zend_Registry::set('Dragon_Package_Registry', $packageregistry);
        return $this;
    }

    /**
     * Initialisiert die Repository Registry zum Verwalten von Repositories
     * @param array $repositories
     * @return Dragon_Application_Application
     */
    public function initRepositoryRegistry(array $repositories)
    {
        $repositoryregistry = new Dragon_Repository_Registry($this, $repositories);
        Zend_Registry::set('Dragon_Repository_Registry', $repositoryregistry);
        return $this;
    }

    /**
     * Initialisiert die Plugin Registry zum Verwalten von Plugins
     * @return Dragon_Application_Application
     */
    public function initPluginRegistry()
    {
        $packageregistry = Zend_Registry::get('Dragon_Package_Registry');
        $pluginregistry = new Dragon_Plugin_Registry($packageregistry->getClassnames('Plugin'));
        Zend_Registry::set('Dragon_Plugin_Registry', $pluginregistry);
        return $this;
    }

    /**
     * Initialisiert die Anwendung und ruft alle Bootstrapper der Pakete auf
     * @return Dragon_Application_Application
     */
    public function bootstrap()
    {
        Zend_Registry::get('Dragon_Plugin_Registry')->invoke(
            'Dragon_Application_Plugin_Bootstrap_Interface'
        );
        return $this;
    }
}
