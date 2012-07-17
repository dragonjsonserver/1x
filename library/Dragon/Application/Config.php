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
     * @var array
     */
    private static $_configs = array();

    /**
     * Prüft das Vorhandensein der Einstellungsdatei und lädt diese
     * @param string $filename
     * @throws InvalidArgumentException
     */
    public function __construct($filename)
    {
        $filepath = APPLICATION_PATH . '/config/' . $filename . '.php';
        if (!isset(self::$_configs[$filepath])) {
            if (!is_file($filepath)) {
                throw new InvalidArgumentException('incorrect filename "' . $filename . '"');
            }
            self::$_configs[$filepath] = require $filepath;
        }
        parent::__construct(self::$_configs[$filepath]);
    }
}
