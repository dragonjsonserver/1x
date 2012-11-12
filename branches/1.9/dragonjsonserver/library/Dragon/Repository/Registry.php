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
 * Klasse zur Verwaltung der aller Repositories
 */
class Dragon_Repository_Registry
{
    /**
     * @var array
     */
    private $_repositories = array();

    /**
     * Fügt mehrere Repositories hinzu zum Abfrufen
     * @param Dragon_Application_Application $application
     * @param array $repositories
     */
    public function __construct(Dragon_Application_Application $application, array $repositories)
    {
    	foreach ($repositories as $directorypath) {
    		$application->addLibrarypath($directorypath . '/library');
    	}
    	$this->_repositories = $repositories;
    }

    /**
     * Gibt den Pfad zum übergebenen Repository zurück
     * @param string $repositoryname
     * @return string
     * @throws InvalidArgumentException
     */
    public function getRepository($repositoryname)
    {
    	if (!isset($this->_repositories[$repositoryname])) {
    		throw new Dragon_Application_Exception('incorrect repositoryname', array('repositoryname' => $repositoryname));
    	}
        return $this->_repositories[$repositoryname];
    }

    /**
     * Gibt alle registrierten Repositories zurück
     * @return array
     */
    public function getRepositories()
    {
        return $this->_repositories;
    }
}
