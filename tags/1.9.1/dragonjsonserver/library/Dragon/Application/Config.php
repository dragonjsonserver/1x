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
 * Klasse zum Laden und Abfragen von Einstellungsdateien
 */
class Dragon_Application_Config extends Zend_Config
{
    /**
     * Prüft das Vorhandensein der Einstellungsdatei und lädt diese
     * @param string $filename
     * @throws InvalidArgumentException
     */
    public function __construct($filename)
    {
        if (Zend_Registry::isRegistered('Dragon_Repository_Registry')) {
            foreach (Zend_Registry::get('Dragon_Repository_Registry')->getRepositories() as $repositoryname => $directorypath) {
                $filepath = $directorypath . '/config/' . $filename . '.php';
                if (is_file($filepath)) {
                    parent::__construct(require $filepath);
                    return;
                }
            }
        }
        $filepath = DRAGONJSONSERVER_PATH . '/config/' . $filename . '.php';
        if (!is_file($filepath)) {
            throw new Dragon_Application_Exception('incorrect configfile', array('filename' => $filename));
        }
        parent::__construct(require $filepath);
    }
}
