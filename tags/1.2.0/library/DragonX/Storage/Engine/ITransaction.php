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
 * Schnittstelle für die Transaktionssteuerung über mehrere Aktionen hinweg
 */
interface DragonX_Storage_Engine_ITransaction
{
    /**
     * Startet eine neue Transaktion zur Ausführung mehrerer SQL Statements
     * @return DragonX_Storage_Engine_Transaction
     */
    public function beginTransaction();

    /**
     * Beendet eine Transaktion mit einem Commit um Änderungen zu schreiben
     * @return DragonX_Storage_Engine_Transaction
     */
    public function commit();

    /**
     * Beendet eine Transaktion mit einem Rollback um Änderungen zurückzusetzen
     * @return DragonX_Storage_Engine_Transaction
     */
    public function rollback();
}
