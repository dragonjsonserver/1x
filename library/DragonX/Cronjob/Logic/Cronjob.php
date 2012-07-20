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
 * Logikklasse zur Verwaltung und Ausführung von Cronjobs
 */
class DragonX_Cronjob_Logic_Cronjob extends DragonX_Database_Logic_Abstract
{
    /**
     * Führt alle Cronjobs aus deren Intervall erreicht wurde
     */
    public function executeCronjobs()
    {
        $modelCronjob = new DragonX_Cronjob_Model_Cronjob();

        $rows = $modelCronjob->getCronjobs();
        $cronjobs = array();
        foreach ($rows as $row) {
            $cronjobs[$row['pluginname']] = $row['lasttimestamp'];
        }

        $pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
        $plugins = $pluginregistry->getPlugins('DragonX_Cronjob_Plugin_Cronjob_Interface');
        foreach ($plugins as $plugin) {
            $pluginname = get_class($plugin);
            $intervall = $plugin->getIntervall();
            if ((
                    !isset($cronjobs[$pluginname])
                    ||
                    (((int)(time() / 60)) - ((int)($cronjobs[$pluginname] / 60))) >= $intervall
                )
                &&
                (((int)(time() / 60)) - $plugin->getOffset()) % $intervall == 0) {
                try {
                    $plugin->execute();
                } catch(Exception $exception) {
                }
                $modelCronjob->updateCronjob($pluginname);
            }
        }
    }
}
