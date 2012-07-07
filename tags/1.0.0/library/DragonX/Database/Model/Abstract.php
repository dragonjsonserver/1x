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
 * Abstrakte Klasse für die Modelschicht der Daten
 */
abstract class DragonX_Database_Model_Abstract
{
    /**
     * @var boolean
     */
    private static $_inTransaction = false;

    /**
     * @var array
     */
    private $_statements = array();

    /**
     * Startet eine neue Transaktion zur Ausführung mehrerer SQL Statements
     * @return boolean
     */
    public function beginTransaction()
    {
        $databaseAdapter = Zend_Registry::get('Zend_Db_Adapter_Abstract');
        if (self::$_inTransaction) {
            return false;
        }
        $databaseAdapter->beginTransaction();
        self::$_inTransaction = true;
        return true;
    }

    /**
     * Beendet eine Transaktion mit einem Commit um Änderungen zu schreiben
     * @return DragonX_Database_Model_Abstract
     */
    public function commit()
    {
        $databaseAdapter = Zend_Registry::get('Zend_Db_Adapter_Abstract');
        if (self::$_inTransaction) {
            self::$_inTransaction = false;
            $databaseAdapter->commit();
        }
    }

    /**
     * Beendet eine Transaktion mit einem Rollback um Änderungen zurückzusetzen
     * @return DragonX_Database_Model_Abstract
     */
    public function rollBack()
    {
        $databaseAdapter = Zend_Registry::get('Zend_Db_Adapter_Abstract');
        if (self::$_inTransaction) {
            self::$_inTransaction = false;
            $databaseAdapter->rollBack();
        }
    }

    /**
     * Führt das übergebene SQL Statement mit den Parametern aus
     * @param string $sql
     * @param array $params
     * @return array|integer|boolean
     */
    protected function _query($sql, array $params = array())
    {
        $databaseAdapter = Zend_Registry::get('Zend_Db_Adapter_Abstract');
        if (!isset($this->_statements[$sql])) {
            $this->_statements[$sql] = $databaseAdapter->prepare($sql);
        }
        $this->_statements[$sql]->execute($params);
        $array = explode(' ', ltrim($sql), 2);
        $querytype = strtoupper(array_shift($array));
        switch ($querytype) {
            case "SELECT":
                return $this->_statements[$sql]->fetchAll();
                break;
            case "INSERT":
                if ($this->_statements[$sql]->rowCount() == 1) {
                    return $databaseAdapter->lastInsertId();
                }
                return true;
                break;
            case "UPDATE":
            case "DELETE":
                return $this->_statements[$sql]->rowCount();
                break;
            default:
                return true;
                break;
        }
    }

    /**
     * Gibt alle Spaltennamen der übergebenen Spalten zurück
     * @param array $columnvalues
     * @return array
     */
    private function _getColumnnames(array $columnvalues)
    {
        return array_keys($columnvalues);
    }

    /**
     * Gibt alle vorbereiteten Spaltennamen der übergebenen Spaltennamen zurück
     * @param array $columnnames
     * @return array
     */
    private function _getPreparedColumnnames(array $columnnames)
    {
        $preparedcolumnnames = array();
        foreach ($columnnames as $columname) {
            $preparedcolumnnames[] = ':' . $columname;
        }
        return $preparedcolumnnames;
    }

    /**
     * Gibt alle vorbereiteten Spaltenpaare der übergebenen Spaltennamen zurück
     * @param array $columnnames
     * @return array
     */
    private function _getPreparedPairs(array $columnnames)
    {
        $preparedupdates = array();
        foreach ($columnnames as $columname) {
            $preparedupdates[] = $columname . ' = :' . $columname;
        }
        return $preparedupdates;
    }

    /**
     * Gibt alle vorbereiteten Spaltenwerte der übergebenen Spalten zurück
     * @param array $preparedcolumnnames
     * @param array $columnvalues
     * @return array
     */
    private function _getPreparedColumnValues(array $preparedcolumnnames, array $columnvalues)
    {
        return array_combine($preparedcolumnnames, array_values($columnvalues));
    }

    /**
     * Einfache Hilfsmethode zur Selektion von Datensätzen
     * @param string $tablename
     * @param array $selectcolumnnames
     * @param array $conditioncolumnvalues
     * @return array
     */
    protected function _select($tablename, array $selectcolumnnames, array $conditioncolumnvalues)
    {
        $conditioncolumnnames = $this->_getColumnnames($conditioncolumnvalues);
        $conditionpreparedcolumnnames = $this->_getPreparedColumnnames($conditioncolumnnames);
        $conditionpreparedcolumnpairs = $this->_getPreparedPairs($conditioncolumnnames);

        $preparedcolumnvalues = $this->_getPreparedColumnValues($conditionpreparedcolumnnames, $conditioncolumnvalues);
        return $this->_query(
              "SELECT " . implode(', ', $selectcolumnnames) . " FROM " . $tablename . " WHERE " . implode(' AND ', $conditionpreparedcolumnpairs),
            $preparedcolumnvalues
        );
    }

    /**
     * Einfache Hilfsmethode zum Einfügen von Datensätzen
     * @param string $tablename
     * @param array $insertcolumnvalues
     * @return integer
     */
    protected function _insert($tablename, array $insertcolumnvalues)
    {
        $insertcolumnnames = $this->_getColumnnames($insertcolumnvalues);
        $insertpreparedcolumnnames = $this->_getPreparedColumnnames($insertcolumnnames);

        $preparedcolumnvalues = $this->_getPreparedColumnValues($insertpreparedcolumnnames, $insertcolumnvalues);
        return $this->_query(
              "INSERT INTO " . $tablename . " (" . implode(', ', $insertcolumnnames) . ") "
            . "VALUES (" . implode(', ', $insertpreparedcolumnnames) . ")",
            $preparedcolumnvalues
        );
    }

    /**
     * Einfache Hilfsmethode zum Ändern von Datensätzen
     * @param string $tablename
     * @param array $updatecolumnvalues
     * @param array $conditioncolumnvalues
     * @return integer
     */
    protected function _update($tablename, array $updatecolumnvalues, array $conditioncolumnvalues)
    {
        $updatecolumnnames = $this->_getColumnnames($updatecolumnvalues);
        $updatepreparedcolumnnames = $this->_getPreparedColumnnames($updatecolumnnames);
        $updatepreparedcolumnpairs = $this->_getPreparedPairs($updatecolumnnames);

        $conditioncolumnnames = $this->_getColumnnames($conditioncolumnvalues);
        $conditionpreparedcolumnnames = $this->_getPreparedColumnnames($conditioncolumnnames);
        $conditionpreparedcolumnpairs = $this->_getPreparedPairs($conditioncolumnnames);

        $preparedcolumnvalues = $this->_getPreparedColumnValues(
            array_merge($updatepreparedcolumnnames, $conditionpreparedcolumnnames),
            array_merge($updatecolumnvalues, $conditioncolumnvalues)
        );
        return $this->_query(
              "UPDATE " . $tablename . " SET " . implode(', ', $updatepreparedcolumnpairs) . " "
            . "WHERE " . implode(' AND ', $conditionpreparedcolumnpairs),
            $preparedcolumnvalues
        );
    }

    /**
     * Einfache Hilfsmethode zum Löschen von Datensätzen
     * @param string $tablename
     * @param array $conditioncolumnvalues
     * @return integer
     */
    protected function _delete($tablename, array $conditioncolumnvalues)
    {
        $conditioncolumnnames = $this->_getColumnnames($conditioncolumnvalues);
        $conditionpreparedcolumnnames = $this->_getPreparedColumnnames($conditioncolumnnames);
        $conditionpreparedcolumnpairs = $this->_getPreparedPairs($conditioncolumnnames);

        $preparedcolumnvalues = $this->_getPreparedColumnValues($conditionpreparedcolumnnames, $conditioncolumnvalues);
        return $this->_query(
              "DELETE FROM " . $tablename . " WHERE " . implode(' AND ', $conditionpreparedcolumnpairs),
            $preparedcolumnvalues
        );
    }

    /**
     * Einfache Hilfsmethode zum Einfügen und Ändern von Datensätzen
     * @param string $tablename
     * @param array $insertupdatecolumnvalues
     * @return integer
     */
    protected function _insertupdate($tablename, array $insertupdatecolumnvalues)
    {
        $insertupdatecolumnnames = $this->_getColumnnames($insertupdatecolumnvalues);
        $insertupdatepreparedcolumnnames = $this->_getPreparedColumnnames($insertupdatecolumnnames);
        $insertupdatepreparedcolumnpairs = $this->_getPreparedPairs($insertupdatecolumnnames);

        $preparedcolumnvalues = $this->_getPreparedColumnValues($insertupdatepreparedcolumnnames, $insertupdatecolumnvalues);
        return $this->_query(
              "INSERT INTO " . $tablename . " (" . implode(', ', $insertupdatecolumnnames) . ") "
            . "VALUES (" . implode(', ', $insertupdatepreparedcolumnnames) . ") "
            . "ON DUPLICATE KEY UPDATE " . implode(', ', $insertupdatepreparedcolumnpairs),
            $preparedcolumnvalues
        );
    }
}
