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
class DragonX_Cronjob_Logic_Cronjob
{
    /**
     * Führt alle Cronjobs aus deren Intervall erreicht wurde
     */
    public function executeCronjobs()
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');

    	$listCronjobs = $storage
    	    ->loadByConditions(new DragonX_Cronjob_Record_Cronjob())
    	    ->groupBy('pluginname');

        $pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
        $plugins = $pluginregistry->getPlugins('DragonX_Cronjob_Plugin_Cronjob_Interface');
        foreach ($plugins as $plugin) {
            $pluginname = get_class($plugin);
            if (isset($listCronjobs[$pluginname])) {
                list($recordCronjob) = $listCronjobs[$pluginname];
            }
            $timestamp = time();
            $intervall = $plugin->getIntervall();
            if ((
                    isset($recordCronjob)
                    &&
                    (((int)($timestamp / 60)) - ((int)($recordCronjob->timestamp / 60))) <= $intervall
                )
                ||
                (((int)($timestamp / 60)) - $plugin->getOffset()) % $intervall > 0) {
                continue;
            }
            try {
                $plugin->execute();
            } catch(Exception $exception) {
            }
            if (isset($recordCronjob)) {
            	$recordCronjob->count += 1;
            	$recordCronjob->timestamp = $timestamp;
            } else {
                $recordCronjob = new DragonX_Cronjob_Record_Cronjob(array(
                	'pluginname' => $pluginname,
                	'count' => 1,
                	'timestamp' => $timestamp
                ));
            }
            $storage->saveRecord($recordCronjob);
        }
    }
}
