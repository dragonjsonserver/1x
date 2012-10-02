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
     */
    public function save(DragonX_Storage_Record_Abstract $record)
    {
        $array = $record->toArray();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subkey => $subvalue) {
                    $array[$key . '_' . $subkey] = $subvalue;
                }
                unset($array[$key]);
            }
        }
    	if (!isset($record->id)) {
            if ($record instanceof DragonX_Storage_Record_Created) {
                $array['created'] = time();
	            if ($record instanceof DragonX_Storage_Record_CreatedModified) {
	            	$array['modified'] = $array['created'];
	            }
            }
            $adapter = $this->getAdapter();
    		$rowCount = $adapter->insert($this->getTablename($record), $array);
    		$record->id = $adapter->lastInsertId();
    	} else {
	        if ($record instanceof DragonX_Storage_Record_CreatedModified) {
                $array['modified'] = time();
	        }
    		$rowCount = $this->getAdapter()->update($this->getTablename($record), $array, 'id = ' . (int)$record->id);
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
    		$count += $this->save($record);
    	}
        return $count;
    }

    /**
     * Lädt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Record_Abstract|boolean
     */
    public function load(DragonX_Storage_Record_Abstract $record)
    {
    	$row = $this->getAdapter()->fetchRow(
    	    "SELECT * FROM `" . $this->getTablename($record) . "` WHERE id = " . (int)$record->id
    	);
    	if ($row) {
    		$record->fromArray($row);
    	} else {
    		unset($record->id);
    		return false;
    	}
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
            $rows = $this->getAdapter()->fetchAssoc(
                "SELECT * FROM `" . $this->getTablename($namespace) . "` WHERE id IN (" . implode(', ', $sublist->getIds()) . ")"
            );
            foreach ($sublist as $record) {
                if (isset($rows[$record->id])) {
                    $record->fromArray($rows[$record->id]);
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
     */
    public function delete(DragonX_Storage_Record_Abstract $record)
    {
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
                $where .= "`" . $key . "` = " . $this->getAdapter()->quote($value) . " AND ";
            }
            $where = substr($where, 0, -5);
        }
    	return $this->loadBySqlStatement($record, "SELECT * FROM `" . $this->getTablename($record) . "`" . $where);
    }

    /**
     * Aktualisiert alle Records welche auf die Bedingungen zutreffen
     * @param DragonX_Storage_Record_Abstract $record
     * @param array $values
     * @param array $conditions
     * @return integer
     */
    public function updateByConditions(DragonX_Storage_Record_Abstract $record, array $values, array $conditions = array())
    {
    	return $this->getAdapter()->update($this->getTablename($record), $values, $conditions);
    }

    /**
     * Entfernt alle Records welche auf die Bedingungen zutreffen
     * @param DragonX_Storage_Record_Abstract $record
     * @param array $conditions
     * @return integer
     */
    public function deleteByConditions(DragonX_Storage_Record_Abstract $record, array $conditions = array())
    {
        return $this->getAdapter()->delete($this->getTablename($record), $conditions);
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
        $rows = $this->getAdapter()->fetchAssoc($sqlstatement, $params);
        $list = new DragonX_Storage_RecordList();
        $classname = get_class($record);
        foreach ($rows as $row) {
            $list[] = new $classname($row);
        }
        return $list;
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
