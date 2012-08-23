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
 * Controller zur Anzeige des Changelogs und der Roadmap
 */
class ChangelogController extends DragonX_Homepage_Controller_ASubnavigation
{
    /**
     * Gibt die Daten für die Subnavigation zurück
     * @return Dragon_Application_Config
     */
    protected function _getSubnavigation()
    {
        return new Dragon_Application_Config('dragonx/homepage/changelog');
    }

    /**
     * Gibt das Verzeichnis für die Einträge der Subnavigation zurück
     * @return string
     */
    protected function _getSubnavigationDirectory()
    {
        return 'changelog';
    }
}
