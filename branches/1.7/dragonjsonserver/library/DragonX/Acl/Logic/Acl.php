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
 * Logikklasse zur Verwaltung der Rollen und Rechte zu den Accounts
 */
class DragonX_Acl_Logic_Acl
{
    /**
     * Gibt die Liste aller Rechte zum übergebenen Account zurück
     * @return array
     */
    public function getResources(Application_Account_Record_Account $recordAccount)
    {
    	$storage = Zend_Registry::get('DragonX_Storage_Engine');

        $voidRecordAccount = new Application_Account_Record_Account();
        $result = $storage->executeSqlStatement(
              "SELECT DISTINCT "
                . "resources.name "
            . "FROM "
                . "application_account_record_account AS account "
	            . "INNER JOIN dragonx_acl_record_accountrole ON account.id = dragonx_acl_record_accountrole.accountid "
	            . "INNER JOIN ( "
	                . "SELECT "
	                    . "n.id AS parentid, "
	                    . "p.id AS nodeid "
	                . "FROM "
	                    . "dragonx_acl_record_role n, "
	                    . "dragonx_acl_record_role p "
	                . "WHERE "
	                    . "n.lft BETWEEN p.lft AND p.rgt "
	            . ") roles ON roles.parentid = dragonx_acl_record_accountrole.roleid "
	            . "INNER JOIN dragonx_acl_record_roleresource ON roles.nodeid = dragonx_acl_record_roleresource.roleid "
	            . "INNER JOIN ( "
	                . "SELECT "
	                    . "n.id AS parentid, o.name "
	                . "FROM "
	                    . "dragonx_acl_record_resource AS n, "
	                    . "dragonx_acl_record_resource AS p, "
	                    . "dragonx_acl_record_resource AS o "
	                . "WHERE "
	                    . "o.lft BETWEEN p.lft AND p.rgt "
	                    . "AND "
	                    . "o.lft BETWEEN n.lft AND n.rgt "
	                . "GROUP BY o.lft "
	            . ") resources ON resources.parentid = dragonx_acl_record_roleresource.resourceid "
            . "WHERE "
                . "account.id = :accountid",
            array('accountid' => $recordAccount->id)
        );
        $resources = array();
        foreach ($result->fetchAll() as $row) {
            $resources[] = $row['name'];
        }
        return $resources;
    }
}
