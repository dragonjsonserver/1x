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
     * Führt einen Cronjob aus auch wenn sein Intervall noch nicht erreicht ist
     * @param string $pluginname
     */
    public function executeCronjob($pluginname)
    {
        $plugin = new $pluginname();
        if (!$plugin instanceof DragonX_Cronjob_Plugin_Cronjob_Interface) {
        	throw new InvalidArgumentException('invalid pluginname');
        }
        $plugin->execute();
        $timestamp = time();
        Zend_Registry::get('DragonX_Storage_Engine')->executeSqlStatement(
              "INSERT INTO `dragonx_cronjob_record_cronjob` (`pluginname`, `count`, `created`, `modified`) "
            . "VALUES (:pluginname, 1, :timestamp, :timestamp) "
            . "ON DUPLICATE KEY UPDATE `count` = `count` + 1, `modified` = :timestamp",
            array('pluginname' => $pluginname, 'timestamp' => $timestamp)
        );
    }

    /**
     * Führt alle Cronjobs aus deren Intervall erreicht wurde
     */
    public function executeCronjobs()
    {
        $storage = Zend_Registry::get('DragonX_Storage_Engine');

        $listCronjobs = $storage
            ->loadByConditions(new DragonX_Cronjob_Record_Cronjob())
            ->indexBy('pluginname', true);

        $pluginregistry = Zend_Registry::get('Dragon_Plugin_Registry');
        $plugins = $pluginregistry->getPlugins('DragonX_Cronjob_Plugin_Cronjob_Interface');
        foreach ($plugins as $plugin) {
            $timestamp = time();
            if ((((int)($timestamp / 60)) - $plugin->getOffset()) % $plugin->getIntervall() > 0) {
                continue;
            }
            try {
                $plugin->execute();
            } catch(Exception $exception) {
            }
            $pluginname = get_class($plugin);
            if (isset($listCronjobs[$pluginname])) {
                $recordCronjob = $listCronjobs[$pluginname];
                $recordCronjob->count += 1;
            } else {
                $recordCronjob = new DragonX_Cronjob_Record_Cronjob(array(
                    'pluginname' => $pluginname,
                    'count' => 1,
                ));
            }
            $storage->save($recordCronjob);
        }
    }
}
