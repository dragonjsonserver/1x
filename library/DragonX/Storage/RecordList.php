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
     * Schränkt die erlaubten Objekte für die RecordList ein
     * @param string $key
     * @param mixed $value
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
     * Entfernt alle Records aus der Liste die neu sind
     * @return DragonX_Storage_RecordList
     */
    public function unsetNewRecords()
    {
        $nullKeys = array();
        foreach ($this as $key => $record) {
            if (!isset($record->id)) {
                $nullKeys[] = $key;
            }
        }
        foreach ($nullKeys as $nullKey) {
            unset($this[$nullKey]);
        }
        return $this;
    }

    /**
     * Entfernt alle Records aus der Liste die geladen wurden
     * @return DragonX_Storage_RecordList
     */
    public function unsetLoadedRecords()
    {
        $nullKeys = array();
        foreach ($this as $key => $record) {
            if (isset($record->id)) {
                $nullKeys[] = $key;
            }
        }
        foreach ($nullKeys as $nullKey) {
            unset($this[$nullKey]);
        }
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
            $ids[] = $record->id;
        }
        return $ids;
    }

    /**
     * Gibt eine neue Liste gruppiert auf die Klassennamen zurück
     * @return DragonX_Storage_RecordList
     */
    public function indexByClassname()
    {
        $list = new DragonX_Storage_RecordList();
        foreach ($this as $record) {
            $classname = get_class($record);
            if (!isset($list[$classname])) {
                $list[$classname] = new DragonX_Storage_RecordList();
            }
            $list[$classname][] = $record;
        }
        return $list;
    }

    /**
     * Gibt eine neue Liste gruppiert auf dem Namespace zurück
     * @return DragonX_Storage_RecordList
     */
    public function indexByNamespace()
    {
        $list = new DragonX_Storage_RecordList();
        foreach ($this as $record) {
            $namespace = $record->getNamespace();
            if (!isset($list[$namespace])) {
                $list[$namespace] = new DragonX_Storage_RecordList();
            }
            $list[$namespace][] = $record;
        }
        return $list;
    }

    /**
     * Gibt eine neue Liste gruppiert auf die Attribute zurück
     * @param string|array $indexby
     * @return DragonX_Storage_RecordList
     */
    public function indexBy($indexby)
    {
        if (!is_array($indexby)) {
            $indexby = array($indexby);
        }
        $list = new DragonX_Storage_RecordList();
        $attributename = array_shift($indexby);
        foreach ($this as $record) {
            $attribute = $record->$attributename;
            if (!isset($list[$attribute])) {
                $list[$attribute] = new DragonX_Storage_RecordList();
            }
            $list[$attribute][] = $record;
        }
        if (count($indexby) > 0) {
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
        foreach ($this as $key => $record) {
            $array[$key] = $record->toArray();
        }
        return $array;
    }
}
