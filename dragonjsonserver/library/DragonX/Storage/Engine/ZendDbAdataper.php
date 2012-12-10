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
class DragonX_Storage_Engine_ZendDbAdataper
    implements DragonX_Storage_Engine_Storage_Interface,
               DragonX_Storage_Engine_Transaction_Interface,
               DragonX_Storage_Engine_Condition_Interface,
               DragonX_Storage_Engine_SqlStatement_Interface
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_adapter;

    /**
     * @var boolean
     */
    private $_transaction;

	/**
     * Nimmt den Datenbankadapter entgegen zur Verwaltung des Storages
     * @param Zend_Db_Adapter_Abstract $adapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter)
    {
    	$this->_adapter = $adapter;
    }

    /**
     * Gibt den Datenbankadapter zur Verwaltung des Storages zurück
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Gibt den Datenbanknamen der Storage Engine zurück
     * @return string
     */
    public function getDatabasename()
    {
    	$config = $this->_adapter->getConfig();
    	return $config['dbname'];
    }

    /**
     * Gibt den Tabellennamen zum übergebenen Record oder Namespace zurück
     * @param mixed $data
     * @return string
     */
    public function getTablename($data)
    {
        if ($data instanceof DragonX_Storage_Record_Abstract) {
            $data = $data->getNamespace();
        }
        return strtolower($data);
    }

    /**
     * Speichert den übergebenen Record im Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return integer
     * @throws InvalidArgumentException
     */
    public function save(DragonX_Storage_Record_Abstract $record)
    {
    	if ($record instanceof DragonX_Storage_Record_ReadOnly_Interface) {
            throw new Dragon_Application_Exception_System('record is readonly', array('recordclass' => get_class($record)));
    	}
        if (!isset($record->id)) {
            if ($record instanceof DragonX_Storage_Record_Created_Abstract) {
                $record->created = time();
                if ($record instanceof DragonX_Storage_Record_CreatedModified_Abstract) {
                    $record->modified = $record->created;
                }
            }
        } else {
            if ($record instanceof DragonX_Storage_Record_CreatedModified_Abstract) {
                $record->modified = time();
            }
        }
    	if (!isset($record->id)) {
            $adapter = $this->getAdapter();
    		$rowCount = $adapter->insert($this->getTablename($record), $record->toArray(false));
    		$record->id = $adapter->lastInsertId();
    	} else {
    		$rowCount = $this->getAdapter()->update($this->getTablename($record), $record->toArray(false), 'id = ' . (int)$record->id);
    	}
        return $rowCount;
    }

    /**
     * Speichert die übergebenen Records im Storage
     * @param DragonX_Storage_RecordList $list
     * @return integer
     */
    public function saveList(DragonX_Storage_RecordList $list)
    {
    	$count = 0;
    	foreach ($list as $record) {
    		try {
    		    $count += $this->save($record);
    		} catch (Exception $exception) {
    		}
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
    	$tablename = $this->getTablename($record);
    	$row = $this->getAdapter()->fetchRow(
    	    "SELECT * FROM `" . $tablename . "` WHERE id = " . (int)$record->id
    	);
    	if (!$row) {
            throw new Dragon_Application_Exception_System('missing record', array('tablenname' => $tablename, 'id' => $record->id));
    	}
		$record->fromArray($row, false);
        return $record;
    }

    /**
     * Lädt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_RecordList
     */
    public function loadList(DragonX_Storage_RecordList $list)
    {
        foreach ($list->indexByNamespace() as $namespace => $sublist) {
            $rows = $this->getAdapter()->fetchAll(
                "SELECT * FROM `" . $this->getTablename($namespace) . "` WHERE id IN (" . implode(', ', $sublist->getIds()) . ")"
            );
            foreach ($sublist as $record) {
                if (isset($rows[$record->id])) {
                    $record->fromArray($rows[$record->id], false);
                } else {
                    unset($record->id);
                }
            }
        }
        return $list;
    }

    /**
     * Entfernt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return integer
     * @throws InvalidArgumentException
     */
    public function delete(DragonX_Storage_Record_Abstract $record)
    {
        if ($record instanceof DragonX_Storage_Record_ReadOnly_Interface) {
            throw new Dragon_Application_Exception_System('record is readonly', array('recordclass' => get_class($record)));
        }
        if (isset($record->id)) {
            $count = $this->getAdapter()->delete($this->getTablename($record), 'id = ' . (int)$record->id);
            unset($record->id);
            return $count;
        }
        return 0;
    }

    /**
     * Entfernt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return integer
     */
    public function deleteList(DragonX_Storage_RecordList $list)
    {
    	$count = 0;
        foreach ($list->indexByNamespace() as $namespace => $sublist) {
        	$sublist->unsetReadOnlyRecords();
            $count += $this->executeSqlStatement(
                "DELETE FROM `" . $this->getTablename($namespace) . "` WHERE id IN (" . implode(', ', $sublist->getIds()) . ")"
            )->rowCount();
            foreach ($sublist as $record) {
                unset($record->id);
            }
        }
        return $count;
    }

    /**
     * Startet eine neue Transaktion zur Ausführung mehrerer SQL Statements
     * @return boolean
     */
    public function beginTransaction()
    {
    	if (!$this->_transaction) {
            $this->getAdapter()->beginTransaction();
            $this->_transaction = true;
            return true;
    	}
    	return false;
    }

    /**
     * Beendet eine Transaktion mit einem Commit um Änderungen zu schreiben
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function commit()
    {
        $this->getAdapter()->commit();
        $this->_transaction = false;
        return $this;
    }

    /**
     * Beendet eine Transaktion mit einem Rollback um Änderungen zurückzusetzen
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function rollback()
    {
        $this->getAdapter()->rollback();
        $this->_transaction = false;
        return $this;
    }

    /**
     * Lädt alle Records welche auf die Bedingungen zutreffen
     * @param DragonX_Storage_Record_Abstract $record
     * @param array $conditions
     * @return DragonX_Storage_RecordList
     */
    public function loadByConditions(DragonX_Storage_Record_Abstract $record, array $conditions = array())
    {
        $where = "";
        if (count($conditions) > 0) {
            $where .= " WHERE ";
            foreach ($conditions as $key => $value) {
	            if (strpos($key, 'NULL') !== false) {
            		$where .= $key;
                } elseif (strpos($key, 'LIKE') !== false) {
                    $where .= $this->getAdapter()->quoteInto($key, $value);
                } elseif (!isset($value)) {
                    $where .= "`" . $key . "` IS NULL";
                } else {
                    $where .= "`" . $key . "` = " . $this->getAdapter()->quote($value);
                }
                $where .= " AND ";
            }
            $where = substr($where, 0, -5);
        }
    	return $this->loadBySqlStatement($record, "SELECT * FROM `" . $this->getTablename($record) . "`" . $where);
    }

    /**
     * Setzt die Bedingungen von Key/Value Paaren auf die Zend Struktur um
     * @param array $conditions
     * @return array
     */
    private function _parseConditions(array $conditions)
    {
        foreach ($conditions as $key => $value) {
            if (strpos($key, 'NULL') !== false || strpos($key, 'LIKE') !== false) {
                continue;
            }
            if (!isset($value)) {
        		unset($conditions[$key]);
                $conditions[$key . ' IS NULL'] = null;
        	} elseif (strpos($key, '?') === false) {
                unset($conditions[$key]);
                $conditions[$key . ' = ?'] = $value;
            }
        }
        return $conditions;
    }

    /**
     * Aktualisiert alle Records welche auf die Bedingungen zutreffen
     * @param DragonX_Storage_Record_Abstract $record
     * @param array $values
     * @param array $conditions
     * @return integer
     * @throws InvalidArgumentException
     */
    public function updateByConditions(DragonX_Storage_Record_Abstract $record, array $values, array $conditions = array())
    {
        if ($record instanceof DragonX_Storage_Record_ReadOnly_Interface) {
            throw new Dragon_Application_Exception_System('record is readonly', array('recordclass' => get_class($record)));
        }
    	return $this->getAdapter()->update($this->getTablename($record), $values, $this->_parseConditions($conditions));
    }

    /**
     * Entfernt alle Records welche auf die Bedingungen zutreffen
     * @param DragonX_Storage_Record_Abstract $record
     * @param array $conditions
     * @return integer
     * @throws InvalidArgumentException
     */
    public function deleteByConditions(DragonX_Storage_Record_Abstract $record, array $conditions = array())
    {
        if ($record instanceof DragonX_Storage_Record_ReadOnly_Interface) {
            throw new Dragon_Application_Exception_System('record is readonly', array('recordclass' => get_class($record)));
        }
        return $this->getAdapter()->delete($this->getTablename($record), $this->_parseConditions($conditions));
    }

    /**
     * Lädt alle Records über das SQL Statement
     * @param DragonX_Storage_Record_Abstract $record
     * @param string $sqlstatement
     * @param array $params
     * @return DragonX_Storage_RecordList
     */
    public function loadBySqlStatement(DragonX_Storage_Record_Abstract $record, $sqlstatement, array $params = array())
    {
        return new DragonX_Storage_RecordList($this->getAdapter()->fetchAll($sqlstatement, $params), $record, false);
    }

    /**
     * Gibt die Werte für eine IN Abfrage zurück
     * @param array $values
     * @return string
     */
    public function getInCondition(array $values)
    {
        $adapter = $this->getAdapter();
        $condition = "";
        foreach ($values as $value) {
            $condition .= $adapter->quote($value) . ", ";
        }
        return "(" . substr($condition, 0, -2) . ")";
    }

    /**
     * Führt ein beliebiges SQL Statement aus
     * @param string $sqlstatement
     * @return Zend_Db_Statement_Interface
     */
    public function executeSqlStatement($sqlstatement, array $params = array())
    {
        return $this->getAdapter()->query($sqlstatement, $params);
    }
}
