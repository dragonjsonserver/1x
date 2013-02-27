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
 * Serviceklasse zur Verwaltung und Ausführung von Cronjobs
 */
class DragonX_Cronjob_Service_Cronjob
{
    /**
     * @var DragonX_Cronjob_Logic_Cronjob
     */
    private $_logicCronjob;

    /**
     * Nimmt die Logikklasse für die Ausführung entgegen
     */
    public function __construct(DragonX_Cronjob_Logic_Cronjob $logicCronjob = null)
    {
        if (!isset($logicCronjob)) {
            $logicCronjob = new DragonX_Cronjob_Logic_Cronjob();
        }
        $this->_logicCronjob = $logicCronjob;
    }

    /**
     * Führt einen Cronjob aus auch wenn sein Intervall noch nicht erreicht ist
     * @param string $securitytoken
     * @param string $pluginname
     * @throws InvalidArgumentException
     */
    public function executeCronjob($securitytoken, $pluginname)
    {
        $configCronjob = new Dragon_Application_Config('dragonx/cronjob/cronjob');
        if ($configCronjob->securitytoken != $securitytoken) {
            throw new Dragon_Application_Exception_User('incorrect securitytoken');
        }
        $this->_logicCronjob->executeCronjob($pluginname);
    }

    /**
     * Führt alle Cronjobs deren Intervall erreicht wurde aus
     * @param string $securitytoken
     * @throws InvalidArgumentException
     */
    public function executeCronjobs($securitytoken)
    {
        $configCronjob = new Dragon_Application_Config('dragonx/cronjob/cronjob');
        if ($configCronjob->securitytoken != $securitytoken) {
            throw new Dragon_Application_Exception_User('incorrect securitytoken');
        }
        $this->_logicCronjob->executeCronjobs();
    }

    /**
     * Gibt den Zeitpunkt zurück wann der Cronjob das nächste mal läuft
     * @param string $pluginname
     * @return integer
     */
    public function getNextTimestamp($pluginname)
    {
    	$reflectionclass = new ReflectionClass($pluginname);
    	if (!$reflectionclass->implementsInterface('DragonX_Cronjob_Plugin_Cronjob_Interface')) {
            throw new Dragon_Application_Exception_User('incorrect pluginname');
    	}
        return $this->_logicCronjob->getNextTimestamp(new $pluginname());
    }
}
