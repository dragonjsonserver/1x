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
     * Befüllt aus den übergebenen Daten und der Vorlage die Recordliste
     * @param array $data
     * @param DragonX_Storage_Record_Abstract $record
     * @param boolean $unsetId
     */
    public function __construct(array $data = array(), DragonX_Storage_Record_Abstract $record = null, $unsetId = true)
	{
        if (isset($record)) {
        	$this->fromArray($data, $record, $unsetId);
        } else {
        	parent::__construct($data);
        }
	}

	/**
     * Befüllt aus den übergebenen Daten und der Vorlage die Recordliste
     * @param array $data
     * @param DragonX_Storage_Record_Abstract $record
     * @param boolean $unsetId
     */
    public function fromArray(array $array, DragonX_Storage_Record_Abstract $record, $unsetId = true)
	{
        $classname = get_class($record);
        foreach ($array as $data) {
            $this[] = new $classname($data, $unsetId);
        }
        return $this;
	}

    /**
     * Gibt das Element der Liste zurück wenn es existiert
     * @param mixed $index
     * @throws InvalidArgumentException
     */
    public function offsetGet($index)
    {
        if (!parent::offsetExists($index)) {
            throw new Dragon_Application_Exception_System('missing record', array('index' => $index));
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
            throw new Dragon_Application_Exception_System('incorrect recordclass', array('recordclass' => get_class($value)));
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
            unset($this[$key]);
        }
        $this->exchangeArray((array)$this->getSublists() + array_values((array)$this->getRecords()));
        return $this;
    }

    /**
     * Entfernt alle Records aus der Liste
     * @return DragonX_Storage_RecordList
     */
    public function unsetRecords()
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if ($value instanceof DragonX_Storage_Record_Abstract) {
                $keys[] = $key;
            }
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Entfernt alle Unterlisten aus der Liste
     * @return DragonX_Storage_RecordList
     */
    public function unsetSublists()
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if ($value instanceof DragonX_Storage_RecordList) {
                $keys[] = $key;
            }
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Entfernt alle Records aus der Liste die neu sind
     * @param boolean $recursive
     * @return DragonX_Storage_RecordList
     */
    public function unsetNewRecords($recursive = true)
    {
        $keys = array();
        foreach ($this as $key => $value) {
        	if ($value instanceof DragonX_Storage_RecordList) {
        		if ($recursive) {
        		    $value->unsetNewRecords($recursive);
        		}
        	} else {
	            if (!isset($value->id)) {
	                $keys[] = $key;
	            }
        	}
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Entfernt alle Records aus der Liste die geladen wurden
     * @param boolean $recursive
     * @return DragonX_Storage_RecordList
     */
    public function unsetLoadedRecords($recursive = true)
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if ($value instanceof DragonX_Storage_RecordList) {
                if ($recursive) {
                    $value->unsetLoadedRecords($recursive);
                }
            } else {
	            if (isset($value->id)) {
	                $keys[] = $key;
	            }
            }
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Entfernt alle ReadOnly Records aus der Liste
     * @param boolean $recursive
     * @return DragonX_Storage_RecordList
     */
    public function unsetReadOnlyRecords($recursive = true)
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if ($value instanceof DragonX_Storage_RecordList) {
                if ($recursive) {
                    $value->unsetReadOnlyRecords($recursive);
                }
            } else {
	                if ($value instanceof DragonX_Storage_Record_ReadOnly_Interface) {
	                $keys[] = $key;
	            }
            }
        }
        $this->unsetKeys($keys);
        return $this;
    }

    /**
     * Entfernt alle IDs der einzelnen Records
     * @param boolean $recursive
     * @return DragonX_Storage_RecordList
     */
    public function unsetIds($recursive = true)
    {
        foreach ($this as $value) {
        	if ($value instanceof DragonX_Storage_RecordList) {
                if ($recursive) {
                    $value->unsetIds($recursive);
                }
            } else {
                unset($value->id);
            }
        }
    }

    /**
     * Gibt alle Einträge aus der Liste mit den übergebenen Keys zurück
     * @param array $keys
     * @param boolean $newkeys
     * @return DragonX_Storage_RecordList
     */
    public function getKeys(array $keys, $newkeys = true)
    {
    	$list = new DragonX_Storage_RecordList();
        foreach ($keys as $key) {
        	if ($newkeys) {
        	   $list[] = $this[$key];
        	} else {
        	   $list[$key] = $this[$key];
        	}
        }
        return $list;
    }

    /**
     * Gibt alle Records aus der Liste zurück
     * @param boolean $newkeys
     * @return DragonX_Storage_RecordList
     */
    public function getRecords($newkeys = true)
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if (!$value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
            $keys[] = $key;
        }
        return $this->getKeys($keys, $newkeys);
    }

    /**
     * Gibt alle Unterlisten aus der Liste zurück
     * @param boolean $newkeys
     * @return DragonX_Storage_RecordList
     */
    public function getSublists($newkeys = true)
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if ($value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
            $keys[] = $key;
        }
        return $this->getKeys($keys, $newkeys);
    }

    /**
     * Gibt alle Records aus der Liste zurück die neu sind
     * @param boolean $newkeys
     * @return DragonX_Storage_RecordList
     */
    public function getNewRecords($newkeys = true)
    {
    	$keys = array();
        foreach ($this as $key => $value) {
            if (!$value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
        	if (!isset($value->id)) {
	            $keys[] = $key;
        	}
        }
        return $this->getKeys($keys, $newkeys);
    }

    /**
     * Gibt alle Records aus der Liste zurück die geladen wurden
     * @param boolean $newkeys
     * @return DragonX_Storage_RecordList
     */
    public function getLoadedRecords($newkeys = true)
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if (!$value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
            if (isset($value->id)) {
                $keys[] = $key;
            }
        }
        return $this->getKeys($keys, $newkeys);
    }

    /**
     * Entfernt alle ReadOnly Records aus der Liste
     * @param boolean $newkeys
     * @return DragonX_Storage_RecordList
     */
    public function getReadOnlyRecords($newkeys = true)
    {
        $keys = array();
        foreach ($this as $key => $value) {
            if (!$value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
            if ($value instanceof DragonX_Storage_Record_ReadOnly_Interface) {
                $keys[] = $key;
            }
        }
        return $this->getKeys($keys, $newkeys);
    }

    /**
     * Gibt die Liste aller IDs der Records zurück
     * @param boolean $newkeys
     * @return array
     */
    public function getIds($newkeys = true)
    {
        $ids = array();
        foreach ($this as $key => $value) {
        	if (!$value instanceof DragonX_Storage_Record_Abstract) {
        		continue;
        	}
            if (isset($value->id)) {
            	if ($newkeys) {
                    $ids[] = $value->id;
            	} else {
                    $ids[$key] = $value->id;
            	}
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
        foreach ($this as $value) {
            if (!$value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
            $classname = get_class($value);
            if ($unique) {
                $list[$classname] = $value;
            } else {
                if (!isset($list[$classname])) {
                    $list[$classname] = new DragonX_Storage_RecordList();
                }
                $list[$classname][] = $value;
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
        foreach ($this as $value) {
            if (!$value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
            $namespace = $value->getNamespace();
            if ($unique) {
                $list[$namespace] = $value;
            } else {
                if (!isset($list[$namespace])) {
                    $list[$namespace] = new DragonX_Storage_RecordList();
                }
                $list[$namespace][] = $value;
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
        foreach ($this as $value) {
            if (!$value instanceof DragonX_Storage_Record_Abstract) {
                continue;
            }
            $attribute = $value->$attributename;
            if (count($indexby) == 0 && $unique) {
                $list[$attribute] = $value;
            } else {
                if (!isset($list[$attribute])) {
                    $list[$attribute] = new DragonX_Storage_RecordList();
                }
                $list[$attribute][] = $value;
            }
        }
        if (count($indexby) > 0) {
            foreach ($list as &$sublist) {
                $sublist = $sublist->indexBy($indexby, $unique);
            }
            unset($sublist);
        }
        return $list;
    }

    /**
     * Gibt eine eindimensionale Liste aller Records zurück
     * @return DragonX_Storage_RecordList
     */
    public function toUnidimensional()
    {
        $list = new DragonX_Storage_RecordList();
        foreach ($this as $key => $value) {
        	if ($value instanceof DragonX_Storage_RecordList) {
        		$sublist = $value->toUnidimensional();
        		foreach ($sublist as $record) {
	        		$list[] = $record;
        		}
        	} else {
        		$list[] = $value;
        	}
        }
        return $list;
    }

    /**
     * Gibt alle als Array konvertierte Records zurück
     * @param boolean $subarrays
     * @return array
     */
    public function toArray($subarrays = true)
    {
        $array = array();
        foreach ($this as $key => $value) {
            $array[$key] = $value->toArray($subarrays);
        }
        return $array;
    }

    /**
     * Erstellt eine Kopie der Liste und setzt alle IDs der Records zurück
     */
    public function __clone()
    {
    	$this->unsetIds();
    }
}
