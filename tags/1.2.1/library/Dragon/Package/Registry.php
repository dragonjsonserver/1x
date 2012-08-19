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
 * Registry um Pakete hinzuzufügen und auszulesen
 */
class Dragon_Package_Registry
{
    /**
     * @var array
     */
    private $_packagenamespaces = array();

    /**
     * Übernimmt mehrere Pakete mit deren Namesräumen
     * @param array $packagenamespaces
     */
    public function __construct(array $packagenamespaces)
    {
        $this->_packagenamespaces = $packagenamespaces;
    }

    /**
     * Gibt alle Pakete mit deren Namensräumen zurück
     * @return array
     */
    public function getPackagenamespaces()
    {
        return $this->_packagenamespaces;
    }

    /**
     * Gibt alle Klassennamen zurück die von dem Klassentyp eingebunden wurden
     * @param string $classtype
     * @return array
     */
    public function getClassnames($classtype)
    {
        $classnames = array();
        foreach ($this->_packagenamespaces as $packagenamespace => $packages) {
            foreach ($packages as $packagename => $package) {
                if (!is_array($package)
                    ||
                    !isset($package[$classtype])) {
                    continue;
                }
                foreach ($package[$classtype] as $packageclassname) {
                    $classnames[] = implode('_', array(
                        $packagenamespace,
                        $packagename,
                        $classtype,
                        $packageclassname,
                    ));
                }
            }
        }
        return $classnames;
    }
}
