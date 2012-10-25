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
 * Liste zur Verwaltung mehrere Records eines Storages
 */
class DragonX_Storage_RecordList extends ArrayObject
{
    /**
     * Gibt das Element der Liste zurück wenn es existiert
     * @param mixed $index
     * @throws InvalidArgumentException
     */
    public function offsetGet($index)
    {
        if (!parent::offsetExists($index)) {
            throw new InvalidArgumentException('missing record');
        }
        return parent::offsetGet($index);
    }

    /**
     * Schränkt die erlaubten Objekte für die RecordList ein
     * @param string $key
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function offsetSet($key, $value)
    {
        if (!$value instanceof DragonX_Storage_Record_Abstract
            &&
            !$value instanceof DragonX_Storage_RecordList) {
            throw new InvalidArgumentException('invalid record');
        }
        parent::offsetSet($key, $value);
    }

    /**
     * Entfernt alle Records aus der Liste mit den übergebenen Keys
     * @return DragonX_Storage_RecordList
     */
    public function unsetKeys(array $keys)
    {
        foreach ($keys as $key) {
            unset($this[$nullKey]);
        }
        $this->exchangeArray(array_values((array)$this));
        return $this;
    }

    /**
     * Entfernt alle Records aus der Liste die neu sind
     * @return DragonX_Storage_RecordList
     */
    public function unsetNewRecords()
    {
        $keys = array();
        foreach ($this as $key => $record) {
            if (!isset($record->id)) {
                $keys[] = $key;
            }
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Entfernt alle Records aus der Liste die geladen wurden
     * @return DragonX_Storage_RecordList
     */
    public function unsetLoadedRecords()
    {
        $keys = array();
        foreach ($this as $key => $record) {
            if (isset($record->id)) {
                $keys[] = $key;
            }
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Entfernt alle ReadOnly Records aus der Liste
     * @return DragonX_Storage_RecordList
     */
    public function unsetReadOnlyRecords()
    {
        $keys = array();
        foreach ($this as $key => $record) {
            if ($record instanceof DragonX_Storage_Record_ReadOnly_Interface) {
                $keys[] = $key;
            }
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Gibt die Liste aller IDs der Records zurück
     * @return array
     */
    public function getIds()
    {
        $ids = array();
        foreach ($this as $record) {
            if (isset($record->id)) {
                $ids[] = $record->id;
            }
        }
        return $ids;
    }

    /**
     * Gibt eine neue Liste gruppiert auf die Klassennamen zurück
     * @param bool $unique
     * @return DragonX_Storage_RecordList
     */
    public function indexByClassname($unique = false)
    {
        $list = new DragonX_Storage_RecordList();
        foreach ($this as $record) {
            $classname = get_class($record);
            if ($unique) {
                $list[$classname] = $record;
            } else {
                if (!isset($list[$classname])) {
                    $list[$classname] = new DragonX_Storage_RecordList();
                }
                $list[$classname][] = $record;
            }
        }
        return $list;
    }

    /**
     * Gibt eine neue Liste gruppiert auf dem Namespace zurück
     * @param bool $unique
     * @return DragonX_Storage_RecordList
     */
    public function indexByNamespace($unique = false)
    {
        $list = new DragonX_Storage_RecordList();
        foreach ($this as $record) {
            $namespace = $record->getNamespace();
            if ($unique) {
                $list[$namespace] = $record;
            } else {
                if (!isset($list[$namespace])) {
                    $list[$namespace] = new DragonX_Storage_RecordList();
                }
                $list[$namespace][] = $record;
            }
        }
        return $list;
    }

    /**
     * Gibt eine neue Liste gruppiert auf die Attribute zurück
     * @param string|array $indexby
     * @param bool $unique
     * @return DragonX_Storage_RecordList
     */
    public function indexBy($indexby, $unique = false)
    {
        if (!is_array($indexby)) {
            $indexby = array($indexby);
        }
        $list = new DragonX_Storage_RecordList();
        $attributename = array_shift($indexby);
        foreach ($this as $record) {
            $attribute = $record->$attributename;
            if ($unique) {
                $list[$attribute] = $record;
            } else {
                if (!isset($list[$attribute])) {
                    $list[$attribute] = new DragonX_Storage_RecordList();
                }
                $list[$attribute][] = $record;
            }
        }
        if (!$unique && count($indexby) > 0) {
            foreach ($list as &$sublist) {
                $sublist = $sublist->indexBy($indexby);
            }
            unset($sublist);
        }
        return $list;
    }

    /**
     * Konvertiert die Records zu Arrays und gibt diese als Array zurück
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this as $key => $value) {
            $array[$key] = $value->toArray();
        }
        return $array;
    }
}
