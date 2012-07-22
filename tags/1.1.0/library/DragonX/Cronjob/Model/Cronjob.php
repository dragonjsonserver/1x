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
 * Modelklasse zur Verwaltung und Speicherung von Cronjobs
 */
class DragonX_Cronjob_Model_Cronjob extends DragonX_Database_Model_Abstract
{
    /**
     * Gibt die Daten aller Cronjobs zurück
     * @return array
     */
    public function getCronjobs()
    {
        return $this->_select(
            'dragonx_cronjob_cronjobs',
            array('pluginname', 'UNIX_TIMESTAMP(lasttimestamp) AS lasttimestamp')
        );
    }

    /**
     * Aktualisiert einen Cronjob nach dessen Ausführung
     * @param string $pluginname
     */
    public function updateCronjob($pluginname)
    {
        $this->_query(
              "INSERT INTO dragonx_cronjob_cronjobs (pluginname, count, firsttimestamp, lasttimestamp) "
            . "VALUES (:pluginname, 1, NOW(), NOW()) "
            . "ON DUPLICATE KEY UPDATE count = count + 1, firsttimestamp = firsttimestamp, lasttimestamp = NOW()",
            array('pluginname' => $pluginname)
        );
    }
}
