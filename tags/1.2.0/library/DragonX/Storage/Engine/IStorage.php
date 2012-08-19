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
interface DragonX_Storage_Engine_IStorage
{
    /**
     * Speichert den übergebenen Record im Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_Storage
     */
    public function save(DragonX_Storage_Record_Abstract $record);

    /**
     * Speichert die übergebenen Records im Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_Storage
     */
    public function saveList(DragonX_Storage_RecordList $list);

    /**
     * Lädt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_Storage
     */
    public function load(DragonX_Storage_Record_Abstract $record);

    /**
     * Lädt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_Storage
     */
    public function loadList(DragonX_Storage_RecordList $list);

    /**
     * Entfernt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_Storage
     */
    public function delete(DragonX_Storage_Record_Abstract $record);

    /**
     * Entfernt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_Storage
     */
    public function deleteList(DragonX_Storage_RecordList $list);
}
