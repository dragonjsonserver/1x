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
class DragonX_Storage_Engine_ZendDbAdataper implements DragonX_Storage_Engine_Interface
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
     * Gibt den Tabellennamen zum übergebenen Record zurück
     * @param DragonX_Storage_Record_Abstract $record
     * @return string
     */
    protected function _getTablename(DragonX_Storage_Record_Abstract $record)
    {
        return strtolower($record->getNamespace());
    }

    /**
     * Speichert den übergebenen Record im Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function saveRecord(DragonX_Storage_Record_Abstract $record)
    {
    	if (!isset($record->id)) {
    		$this->_getAdapter()->insert($this->_getTablename($record), $record->toArray());
    		$record->id = $this->_getAdapter()->lastInsertId();
    	} else {
    		$this->_getAdapter()->update($this->_getTablename($record), $record->toArray(), 'id = ' . (int)$record->id);
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
    		$this->saveRecord($record);
    	}
        return $this;
    }

    /**
     * Lädt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function loadRecord(DragonX_Storage_Record_Abstract $record)
    {
    	$result = $this->_getAdapter()->fetchRow(
    	    "SELECT * FROM `" . $this->_getTablename($record) . "` WHERE id = " . (int)$record->id,
    	    Zend_Db::FETCH_ASSOC
    	);
    	if ($result) {
    		$record->fromArray($result);
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
        foreach ($list as $record) {
            $this->loadRecord($record);
        }
        return $this;
    }

    /**
     * Entfernt den übergebenen Record aus dem Storage
     * @param DragonX_Storage_Record_Abstract $record
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function deleteRecord(DragonX_Storage_Record_Abstract $record)
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
        foreach ($list as $record) {
            $this->deleteRecord($record);
        }
        return $this;
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
     * @return DragonX_Storage_Engine_ZendDbAdataper
     */
    public function loadByConditions(DragonX_Storage_Record_Abstract $record, array $conditions = array())
    {
    	$condition = "";
    	if (count($conditions) > 0) {
    		$condition .= " WHERE ";
    		foreach ($conditions as $key => $value) {
                $condition .= "`" . $key . "` = " . $this->_getAdapter()->quote($value) . " AND ";
    		}
    		$condition = substr($condition, 0, -5);
    	}
    	return $this->loadBySqlStatement($record, "SELECT * FROM `" . $this->_getTablename($record) . "`" . $condition);
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
