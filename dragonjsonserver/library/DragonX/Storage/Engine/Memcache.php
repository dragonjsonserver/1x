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
    implements DragonX_Storage_Engine_Storage_Interface,
               DragonX_Storage_Engine_Flush_Interface
{
    /**
     * @var Memcache
     */
    private $_memcache;

    /**
     * @var string
     */
    private $_namespace;

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
    public function getMemcache()
    {
        return $this->_memcache;
    }

    /**
     * Setzt den Namspace den jeder Key vorangestellt bekommt
     * @param string $namespace
     * @return DragonX_Storage_Engine_Memcache
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * Gibt den Namspace zurück den jeder Key vorangestellt bekommt
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Gibt den Keynamen für den Record zurück
     * @param DragonX_Storage_Record_Abstract $record
     * @return string
     */
    protected function _getKey(DragonX_Storage_Record_Abstract $record)
    {
        $namespace = $this->getNamespace();
        if (isset($namespace)) {
            $namespace .= '|';
        }
        return $namespace . $record->getNamespace() . '|' . $record->id;
    }

    /**
     * Speichert den übergebenen Record im Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return integer
     */
    public function save(DragonX_Storage_Record_Abstract $record)
    {
        if (!isset($record->id)) {
            if ($record instanceof DragonX_Storage_Record_Created_Abstract) {
                $record->created = time();
                if ($record instanceof DragonX_Storage_Record_CreatedModified_Abstract) {
                    $record->modified = $record->created;
                }
            }
            $record->id = uniqid();
        } else {
            if ($record instanceof DragonX_Storage_Record_CreatedModified_Abstract) {
                $record->modified = time();
            }
        }
        $this->getMemcache()->set($this->_getKey($record), $record->toArray());
        return 1;
    }

    /**
     * Speichert die übergebenen Records im Storage
     * @param DragonX_Storage_RecordList $list
     * @param boolean $recursive
     * @return integer
     */
    public function saveList(DragonX_Storage_RecordList $list, $recursive = true)
    {
        $count = 0;
        if ($recursive) {
            $list = $list->toUnidimensional();
        } else {
            $list = $list->getRecords();
        }
        foreach ($list as $record) {
            $count += $this->save($record);
        }
        return $count;
    }

    /**
     * Lädt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Record_Abstract
     * @throw InvalidArgumentException
     */
    public function load(DragonX_Storage_Record_Abstract $record)
    {
        $key = $this->_getKey($record);
        $result = $this->getMemcache()->get($key);
        if (!$result) {
            throw new Dragon_Application_Exception_System('missing record', array('key' => $key));
        }
        $record->fromArray($result, false);
        return $record;
    }

    /**
     * Lädt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @param boolean $recursive
     * @return DragonX_Storage_RecordList
     */
    public function loadList(DragonX_Storage_RecordList $list, $recursive = true)
    {
        if ($recursive) {
            $list = $list->toUnidimensional();
        } else {
            $list = $list->getRecords();
        }
        foreach ($list as $record) {
            try {
                $this->load($record);
            } catch (Exception $exception) {
                unset($record->id);
            }
        }
        return $list;
    }

    /**
     * Entfernt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return integer
     */
    public function delete(DragonX_Storage_Record_Abstract $record)
    {
        if (isset($record->id)) {
            $this->getMemcache()->delete($this->_getKey($record));
            unset($record->id);
            return 1;
        }
        return 0;
    }

    /**
     * Entfernt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @param boolean $recursive
     * @return integer
     */
    public function deleteList(DragonX_Storage_RecordList $list, $recursive = true)
    {
        $count = 0;
        if ($recursive) {
            $list = $list->toUnidimensional();
        } else {
            $list = $list->getRecords();
        }
        foreach ($list as $record) {
            $count += $this->delete($record);
        }
        return $count;
    }

    /**
     * Entfernt alle Records aus der Storage Engine
     * @return DragonX_Storage_Engine_Memcache
     */
    public function flush()
    {
        $this->getMemcache()->flush();
    }
}
