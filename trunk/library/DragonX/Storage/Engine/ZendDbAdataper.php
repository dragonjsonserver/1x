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
    implements DragonX_Storage_Engine_IStorage,
               DragonX_Storage_Engine_ITransaction,
               DragonX_Storage_Engine_ICondition,
               DragonX_Storage_Engine_ISqlStatement
{
	/**
     * @var Zend_Db_Adapter_Abstract
     */
	private $_adapter;

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
    protected function _getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Gibt den Tabellennamen zum übergebenen Record oder Namespace zurück
     * @param mixed $data
     * @return string
     */
    protected function _getTablename($data)
    {
        if ($data instanceof DragonX_Storage_Record_Abstract) {
            $data = $data->getNamespace();
        }
        return strtolower($data);
    }

    /**
     * Speichert den übergebenen Record im Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function save(DragonX_Storage_Record_Abstract $record)
    {
    	if (!isset($record->id)) {
    		$this->_getAdapter()->insert($this->_getTablename($record), $record->toArray());
    		$record->id = $this->_getAdapter()->lastInsertId();
    	} else {
    		$rowCount = $this->_getAdapter()->update($this->_getTablename($record), $record->toArray(), 'id = ' . (int)$record->id);
    		if ($rowCount == 0) {
    		    unset($record->id);
    		}
    	}
        return $this;
    }

    /**
     * Speichert die übergebenen Records im Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_ZendDbAdataper
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
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function load(DragonX_Storage_Record_Abstract $record)
    {
    	$rows = $this->_getAdapter()->fetchAssoc(
    	    "SELECT * FROM `" . $this->_getTablename($record) . "` WHERE id = " . (int)$record->id
    	);
    	if (count($rows) > 0) {
    		$record->fromArray($rows[0]);
    	} else {
    		unset($record->id);
    	}
        return $this;
    }

    /**
     * Lädt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function loadList(DragonX_Storage_RecordList $list)
    {
        foreach ($list->indexByNamespace() as $namespace => $sublist) {
            $rows = $this->_getAdapter()->fetchAssoc(
                "SELECT * FROM `" . $this->_getTablename($namespace) . "` WHERE id IN (" . implode(', ', $sublist->getIds()) . ")"
            );
            $indexRows = array();
            foreach ($rows as $row) {
                $indexRows[(int)$row] = $row;
            }
            foreach ($sublist as $record) {
                if (isset($indexRows[$record->id])) {
                    $record->fromArray($indexRows[$record->id]);
                } else {
                    unset($record->id);
                }
            }
        }
        return $this;
    }

    /**
     * Entfernt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function delete(DragonX_Storage_Record_Abstract $record)
    {
        if (isset($record->id)) {
            $this->_getAdapter()->delete($this->_getTablename($record), 'id = ' . (int)$record->id);
            unset($record->id);
        }
        return $this;
    }

    /**
     * Entfernt die übergebenen Records aus dem Storage
     * @param DragonX_Storage_RecordList $list
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function deleteList(DragonX_Storage_RecordList $list)
    {
        foreach ($list->indexByNamespace() as $namespace => $sublist) {
            $this->executeSqlStatement(
                "DELETE FROM `" . $this->_getTablename($namespace) . "` WHERE id IN (" . implode(', ', $sublist->getIds()) . ")"
            );
            foreach ($sublist as $record) {
                unset($record->id);
            }
        }
    }

    /**
     * Startet eine neue Transaktion zur Ausführung mehrerer SQL Statements
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function beginTransaction()
    {
        $this->_getAdapter()->beginTransaction();
        return $this;
    }

    /**
     * Beendet eine Transaktion mit einem Commit um Änderungen zu schreiben
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function commit()
    {
        $this->_getAdapter()->commit();
        return $this;
    }

    /**
     * Beendet eine Transaktion mit einem Rollback um Änderungen zurückzusetzen
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function rollback()
    {
        $this->_getAdapter()->rollback();
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
                $where .= "`" . $key . "` = " . $this->_getAdapter()->quote($value) . " AND ";
            }
            $where = substr($where, 0, -5);
        }
    	return $this->loadBySqlStatement($record, "SELECT * FROM `" . $this->_getTablename($record) . "`" . $where);
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
    	return $this->_getAdapter()->update($this->_getTablename($record), $values, $conditions);
    }

    /**
     * Entfernt alle Records welche auf die Bedingungen zutreffen
     * @param DragonX_Storage_Record_Abstract $record
     * @param array $conditions
     * @return integer
     */
    public function deleteByConditions(DragonX_Storage_Record_Abstract $record, array $conditions = array())
    {
        return $this->_getAdapter()->delete($this->_getTablename($record), $conditions);
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
        $rows = $this->_getAdapter()->fetchAssoc($sqlstatement, $params);
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
        return $this->_getAdapter()->query($sqlstatement, $params);
    }
}
