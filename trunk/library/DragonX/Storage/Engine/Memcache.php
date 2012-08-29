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
 * Storage Engine zur Verwaltung von Records in einer relationalen Datenbank
 */
class DragonX_Storage_Engine_Memcache
    implements DragonX_Storage_Engine_IStorage,
               DragonX_Storage_Engine_IFlush
{
    /**
     * @var Memcache
     */
    private $_memcache;

    /**
     * Nimmt den Memcache entgegen zur Verwaltung des Storages
     * @param Memcache $memcache
     */
    public function __construct(Memcache $memcache)
    {
        $this->_memcache = $memcache;
    }

    /**
     * Gibt den Memcache zur Verwaltung des Storages zurück
     * @return Memcache
     */
    protected function _getMemcache()
    {
        return $this->_memcache;
    }

    /**
     * Gibt den Keynamen für den Record zurück
     * @param DragonX_Storage_Record_Abstract $record
     * @return string
     */
    protected function _getKey(DragonX_Storage_Record_Abstract $record)
    {
        return $record->getNamespace() . '|' . $record->id;
    }

    /**
     * Speichert den übergebenen Record im Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_Memcache
     */
    public function save(DragonX_Storage_Record_Abstract $record)
    {
        if (!isset($record->id)) {
            $record->id = uniqid();
        }
        $this->_getMemcache()->set($this->_getKey($record), $record->toArray());
        return $this;
    }

    /**
     * Speichert die übergebenen Records im Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_Memcache
     */
    public function saveList(DragonX_Storage_RecordList $list)
    {
        foreach ($list as $record) {
            $this->save($record);
        }
        return $this;
    }

    /**
     * Lädt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Record_Abstract|boolean
     */
    public function load(DragonX_Storage_Record_Abstract $record)
    {
        $result = $this->_getMemcache()->get($this->_getKey($record));
        if ($result) {
            $record->fromArray($result);
        } else {
            unset($record->id);
            return false;
        }
        return $record;
    }

    /**
     * Lädt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_Memcache
     */
    public function loadList(DragonX_Storage_RecordList $list)
    {
        foreach ($list as $record) {
            $this->load($record);
        }
        return $this;
    }

    /**
     * Entfernt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_Memcache
     */
    public function delete(DragonX_Storage_Record_Abstract $record)
    {
        if (isset($record->id)) {
            $this->_getMemcache()->delete($this->_getKey($record));
            unset($record->id);
        }
        return $this;
    }

    /**
     * Entfernt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_Memcache
     */
    public function deleteList(DragonX_Storage_RecordList $list)
    {
        foreach ($list as $record) {
            $this->delete($record);
        }
        return $this;
    }

    /**
     * Entfernt alle Records aus der Storage Engine
     * @return DragonX_Storage_Engine_Memcache
     */
    public function flush()
    {
        $this->_getMemcache()->flush();
    }
}
