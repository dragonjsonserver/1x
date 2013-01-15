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
 * Schnittstelle mit denen man Records speichern und laden kann
 */
interface DragonX_Storage_Engine_Storage_Interface
{
    /**
     * Speichert den übergebenen Record im Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return integer
     */
    public function save(DragonX_Storage_Record_Abstract $record);

    /**
     * Speichert die übergebenen Records im Storage
     * @param DragonX_Storage_RecordList $list
     * @param boolean $recursive
     * @return integer
     */
    public function saveList(DragonX_Storage_RecordList $list, $recursive = true);

    /**
     * Lädt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Record_Abstract
     * @throw InvalidArgumentException
     */
    public function load(DragonX_Storage_Record_Abstract $record);

    /**
     * Lädt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @param boolean $recursive
     * @return DragonX_Storage_RecordList
     */
    public function loadList(DragonX_Storage_RecordList $list, $recursive = true);

    /**
     * Entfernt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return integer
     */
    public function delete(DragonX_Storage_Record_Abstract $record);

    /**
     * Entfernt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @param boolean $recursive
     * @return integer
     */
    public function deleteList(DragonX_Storage_RecordList $list, $recursive = true);
}
